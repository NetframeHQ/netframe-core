<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\BaseController;
use App\Observers\Searchable;

//use AppRepository\Profile;

class NewsFeed extends Model
{
    use CacheQueryBuilder;

    /*
     * add Elasticsearch as observer
     */
    use Searchable;

    protected $table = "news_feeds";

    protected $fillable = array(
        'users_id',
        'author_id',
        'author_type',
        'post_id',
        'post_type',
        'share',
        'like'
    );

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($newsfeed) {
            $newsfeed->channels()->detach();

            $newsfeed->post()->get()->each(function ($post) {
                if (class_basename($post) != 'TaskTable') {
                    $post->delete();
                }
            });
        });
    }


    /*
     * relations with newsfeed (for reading messages status)
     */
    public function channels()
    {
        return $this->belongsToMany('App\Channel', 'channels_has_news_feeds', 'news_feeds_id', 'channels_id')
        ->withPivot('read', 'users_id')
        ->withTimestamps();
    }

    /**
     * morph relation to profile or user feed page
     */
    public function author()
    {
        return $this->morphTo();
    }

    /**
     * morph relation to profile or user feed page
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function true_author()
    {
        return $this->morphTo();
    }

    /**
     * morph relation to posts contained in newsfeed (news, events, playlists, shares, netframe_actions)
     */
    public function post()
    {
        return $this->morphTo();
    }

    public static function getTableName()
    {
        return with(new static())->getTable();
    }

    /**
     *
     * @param unknown $idForeign
     * @param unknown $typeForeign
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profile($idForeign, $typeForeign)
    {
        return $this->hasMany($typeForeign);
    }

    /*
     * Relation with user
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id')
        ->whereHas('instances', function ($wI) {
            $wI->where('id', '=', session('instanceId'));
        });
    }

    /**
     * @TODO $type_foreign is unused
     *
     * @param $id --> profile id
     * @param $type_foreign --> type de profil (house, community, user, project)
     * @param $profile --> objet profile (House, Community, User, Project)
     * @param $date_time_last -> date time au format sql pour l'infinite scroll
     * @param $idNewsFeed --> id de la table news_feeds pour afficher la page avec un seul psot et tous ses commentaires
     */
    public static function getByProfileMorph(
        $id,
        $type_foreign,
        $profile,
        $date_time_last = null,
        $idNewsFeed = null,
        $userTimeline = false
    ) {
        $config = config('netframe');

        if (get_class($profile) != 'App\User') {
            //return $profile->posts()
            return NewsFeed::where('instances_id', '=', session('instanceId'))
                //->where('news_feeds.post_type', '!=', 'App\TaskTable')
                ->where(function ($wAuthor) use ($profile) {
                    $wAuthor->orWhere(function ($wA) use ($profile) {
                        $wA->where('author_id', '=', $profile->id)
                           ->where('author_type', '=', get_class($profile));
                    });
                    /*
                    ->orWhere(function($wTa) use($profile){
                        $wTa->where('true_author_id', '=', $profile->id)
                           ->where('true_author_type', '=', get_class($profile));
                    });
                    */
                })
                ->where(function ($where) use ($date_time_last) {
                    if ($date_time_last !== null) {
                        $where->where('news_feeds.created_at', '<', $date_time_last);
                    }
                })
                ->where(function ($where) use ($idNewsFeed) {
                    if ($idNewsFeed !== null) {
                        $where->where('news_feeds.id', '=', $idNewsFeed);
                    }
                })
                ->where(function ($where) use ($profile) {
                    if (BaseController::hasRightsProfile($profile)
                        || get_class($profile) == 'Project'
                        || get_class($profile) == 'Community') {
                        $where->where('invisible_owner', '=', 0);
                    }
                })
                ->where(function ($where) use ($profile) {
                    $confidentiality = 1;
                    if (BaseController::hasViewProfile($profile) || BaseController::hasRightsProfile($profile)) {
                        $confidentiality = 0;
                    }
                    $where->where('confidentiality', '>=', $confidentiality);
                })

                // make a join with user comments, likes

                ->where('active', '=', '1')
                ->where(function ($wherePt) use ($idNewsFeed) {
                    if ($idNewsFeed == null) {
                        $wherePt->where('pintop', '!=', '1');
                    }
                })
                ->where('pintop', '!=', '1')
                ->groupBy('news_feeds.id')
                ->orderBy('created_at', 'desc')
                ->take($config['number_post'])
                ->with([
                    'post',
                    'views',
                    'author',
                    'true_author',
                    'post.liked',
                    'post.medias',
                    'post.medias.archives',
                    'post.medias.views',
                    'post.medias.folderRel',
                    'post.author',
                    'post.comments',
                    'post.comments.replies',
                    'post.comments.replies.replies',
                    'post.lastComments',
                    'post.lastComments.replies',
                    'post.lastComments.replies.replies',
                ])
                ->get();
        } elseif (!$userTimeline) { // user public page
            return NewsFeed::select('news_feeds.*')
                //->where('news_feeds.post_type', '!=', 'App\TaskTable')
                ->where('news_feeds.author_id', '=', $id)
                ->where('news_feeds.author_type', '=', 'App\User')
                /*
                ->where(function($whereInvisibility) use ($id) {
                    if(auth()->guard('web')->check() && $id == auth()->guard('web')->user()->id){
                        $whereInvisibility->where('invisible_owner', '=', '0');
                    }
                })
                */
                ->where(function ($where) use ($date_time_last) {
                    if ($date_time_last !== null) {
                        $where->where('news_feeds.created_at', '<', $date_time_last);
                    }
                })
                ->where(function ($where) use ($idNewsFeed) {
                    if ($idNewsFeed !== null) {
                        $where->where('news_feeds.id', '=', $idNewsFeed);
                    }
                })

                ->where('active', '=', '1')
                ->groupBy('news_feeds.post_type')
                ->groupBy('news_feeds.post_id')
                ->orderBy('news_feeds.created_at', 'desc')
                ->take($config['number_post'])
                ->with([
                    'post',
                    'views',
                    'author',
                    'true_author',
                    'post.liked',
                    'post.author',
                    'post.medias',
                    'post.medias.archives',
                    'post.medias.views',
                    'post.medias.folderRel',
                    'post.comments',
                    'post.comments.replies',
                    'post.comments.replies.replies',
                    'post.lastComments',
                    'post.lastComments.replies',
                    'post.lastComments.replies.replies',
                ])
                ->get();
        } else { // user timeline
            return NewsFeed::select('news_feeds.*')
                //->where('news_feeds.post_type', '!=', 'App\TaskTable')
                ->leftJoin('subscriptions as sub', function ($joinS) {
                    $joinS->on('sub.profile_type', '=', 'news_feeds.author_type')
                    ->on('sub.profile_id', '=', 'news_feeds.author_id');
                })
                ->leftJoin('friends as f1', function ($joinS) {
                    $joinS->on('f1.users_id', '=', 'news_feeds.author_id');
                })
                ->leftJoin('friends as f2', function ($joinS) {
                    $joinS->on('f2.friends_id', '=', 'news_feeds.author_id');
                })
                ->leftJoin('events_has_friends as ehf', function ($joinE) {
                    $joinE->on('ehf.events_id', '=', 'news_feeds.post_id')
                    ->where('news_feeds.post_type', '=', 'App\\TEvent');
                })
                ->where('news_feeds.instances_id', '=', session('instanceId'))
                ->where(function ($where) use ($date_time_last) {
                    if ($date_time_last !== null) {
                        $where->where('news_feeds.updated_at', '<', $date_time_last);
                    }
                })
                ->where(function ($where) use ($id) {
                    $where->orWhere(function ($whereS) use ($id) {
                        $whereS->where('sub.users_id', '=', $id)
                            ->where('news_feeds.confidentiality', '>=', 'sub.confidentiality');
                    })
                    ->orWhere(function ($whereE) use ($id) {
                        $whereE->where('ehf.users_id', '=', $id);
                    })
                    ->orWhere(function ($whereF) use ($id) {
                        $whereF->orWhere(function ($wf1) use ($id) {
                            $wf1->where('f1.friends_id', '=', $id)
                                ->where('news_feeds.author_type', '=', 'App\\User')
                                ->where('f1.blacklist', '=', 0)
                                ->where('f1.status', '=', 1);
                        })
                        ->orWhere(function ($wf2) use ($id) {
                            $wf2->where('f2.users_id', '=', $id)
                                ->where('news_feeds.author_type', '=', 'App\\User')
                                ->where('f2.blacklist', '=', 0)
                                ->where('f2.status', '=', 1);
                        });
                    });
                })
                ->where('private_profile', '=', 0)
                ->where('news_feeds.active', '=', '1')
                //->where('invisible_owner', '=', '0')
                ->groupBy('news_feeds.post_type')
                ->groupBy('news_feeds.post_id')
                ->orderBy('news_feeds.updated_at', 'desc')
                ->take($config['number_post'])
                ->with([
                    'post',
                    'views',
                    'author',
                    'true_author',
                    'post.liked',
                    'post.author',
                    'post.medias',
                    'post.medias.archives',
                    'post.medias.views',
                    'post.medias.folderRel',
                    'post.comments',
                    'post.comments.replies',
                    'post.comments.replies.replies',
                    'post.lastComments',
                    'post.lastComments.replies',
                    'post.lastComments.replies.replies',
                ])
                ->get();
        }
    }

    /**
     * Check if pintop already exist for this profile
     *
     * @return boolean true / false
     */
    public function pinTopExist($idForeign, $typeForeign)
    {
        $query = \DB::table($this->table)->where('pintop', '=', 1)
            ->where('author_id', '=', $idForeign)
            ->where('author_type', '=', $typeForeign)
            ->exists();

        return $query;
    }

    public static function lastNews($typeNews = 'News', $limit = 5)
    {
        return NewsFeed::where('post_type', '=', $typeNews)
                //->where('news_feeds.post_type', '!=', 'App\TaskTable')
                ->where('news_feeds.instances_id', '=', session('instanceId'))
                ->whereConfidentiality(1)
                ->whereActive(1)
                ->orderBy('created_at', 'desc')
                ->groupBy('author_id')
                ->groupBy('author_type')
                ->take($limit)
                ->with(['post', 'author'])
                ->get();
    }

    public function views()
    {
        return $this->morphMany('App\View', 'post');
    }

    public function view()
    {
        $userId = auth()->guard('web')->user()->id;
        if (!($this->author_type=="App\\User" && $this->author_id==$userId)) {
            $view = View::where([
                'post_id' => $this->id,
                'post_type' => get_class($this),
                'users_id' => $userId
            ])->first();

            if (!$view) {
                \DB::table('views')->insert([
                    'post_id' => $this->id,
                    'post_type' => get_class($this),
                    'users_id' => $userId
                ]);
            }
        }
    }
}
