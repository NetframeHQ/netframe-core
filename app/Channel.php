<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Observers\Searchable;

class Channel extends Model
{
    use CacheQueryBuilder;

    /*
     * add Elasticsearch as observer
    */
    use Searchable;
    protected $table = "channels";
    protected $type = "channel";
    public $rolesLangKey = 'members.roles.';

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($channel) {

            $channel->newsfeed()->detach();

            $channel->posts()->get()->each(function ($post) {
                $post->delete();
            });

            $channel->actions()->get()->each(function ($action) {
                $action->delete();
            });

            $channel->tags()->detach();
            $channel->users()->detach();

            // delete notifications
            $notification = Notif::where('type', 'LIKE', 'joinChannel%')
                ->where('parameter', 'LIKE', '%profile_id":"'.$channel->id.'"%')
                ->delete();
            $notification = Notif::where('type', 'LIKE', 'inviteChannel%')
                ->where('parameter', 'LIKE', '%profile_id":"'.$channel->id.'"%')
                ->delete();
        });
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNameDisplay()
    {
        if ($this->personnal == 0) {
            return $this->name;
        } else {
            // serach other user
            //$otherUser = $this->users()->where('users_id', '!=', auth()->guard('web')->user()->id)->first();
            return $this->otherUser()->getNameDisplay();
        }
    }

    public function lastConnnect()
    {
        if ($this->personnal == 1) {
            return $this->otherUser()->last_connexion;
        }
        return '';
    }

    public function otherUser()
    {
        $otherUser = $this->users()->where('users_id', '!=', auth()->guard('web')->user()->id)->first();
        return $otherUser;
    }

    public function getUserPhoto()
    {
        //$otherUser = $this->users()->where('users_id', '!=', auth()->guard('web')->user()->id)->first();
        return ($this->otherUser()->profileImage != null) ? $this->otherUser()->profileImage->id : null;
    }

    public function getUserStatus()
    {
        $otherUser = $this->users()->where('users_id', '!=', auth()->guard('web')->user()->id)->first();
        return ($otherUser->isOnline()) ? true : false;
    }

    public function profileImageSrc()
    {
        return url()->route('netframe.svgicon', ['name' => $this->getType()]);
    }

    public function mosaicImage()
    {
        return null;
    }

    public function getUrl()
    {
        return url()->route('channels.home', ['id' => $this->id]);
    }

    public function getUrlNotif()
    {
        return $this->getUrl();
    }

    /**
     * morph relation with news profile owner
     */
    public function profile()
    {
        return $this->morphTo();
    }

    /**
     * morph relation with newsfeed of profile
     */
    public function posts()
    {
        return $this->morphMany('App\NewsFeed', 'author');
    }

    /**
     * morph relation with newsfeed of profile
     */
    public function truePosts()
    {
        return $this->morphMany('App\NewsFeed', 'true_author');
    }

    /**
     * morph relation with news of profile
     */
    public function news()
    {
        return $this->morphMany('App\News', 'author');
    }

    /*
     * relations with newsfeed (for reading messages status)
     */
    public function newsfeed()
    {
        return $this->belongsToMany('App\NewsFeed', 'channels_has_news_feeds', 'channels_id', 'news_feeds_id')
        ->withPivot('read', 'users_id')
        ->withTimestamps();
    }

    /**
     * morph relation when profile make actions
     */
    public function actions()
    {
        return $this->morphMany('App\NetframeAction', 'author');
    }

    public function medias()
    {
        return $this->belongsToMany('App\Media', 'channels_has_medias', 'channels_id', 'medias_id')
            ->where('under_workflow', '=', 0);
    }

    public function allMedias()
    {
        return $this->belongsToMany('App\Media', 'channels_has_medias', 'channels_id', 'medias_id');
    }

    public function lastMedias($nb = 3)
    {
        return $this->medias()->orderBy('created_at', 'desc')->take($nb)->get();
    }

    public function mediasFolders()
    {
        return false;
    }

    /*
     * relations with users
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'channels_has_users', 'channels_id', 'users_id')
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function validatedUsers()
    {
        return $this->belongsToMany('App\User', 'channels_has_users', 'channels_id', 'users_id')
            ->wherePivot('status', '=', 1)
            ->where('active', '=', 1)
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function nbUsers()
    {
        return $this->validatedUsers()->count();
    }

    /*
     * relation with user creator
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id')
            ->whereHas('instances', function ($wI) {
                $wI->where('id', '=', session('instanceId'));
            });
    }

    /**
     * morph relation with tags
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function tagsList($onlyIds = false)
    {
        $tagsTab = [];
        foreach ($this->tags as $tag) {
            if ($onlyIds) {
                $tagsTab[] =$tag->id;
            } else {
                $tagsTab[$tag->id] = $tag->name;
            }
        }
        return $tagsTab;
    }

    /*
     * relation with instances
     */
    public function instances()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function toReducedArray()
    {
        $users = [];
        foreach ($this->users()->getResults() as $user) {
            $users[] = $user->toReducedArray();
        }

        return [
            "id" => $this->id,
            "active" => $this->active,
            "name" => $this->name,
            "description" => $this->description,
            "confidentiality" => $this->confidentiality,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "instance" => $this->instances_id,
            "personnal" => $this->personnal,
            "profile_media_id" => $this->profile_media_id,
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
            "url" => parse_url($this->getUrl())['path'],
            "users" => $users,
        ];
    }

    public static function mapping()
    {
        $index = self::first()->getSearchIndex();
        $type = self::first()->getSearchType();
        return [
            'index' => $index,
            'body' => [
                'settings' => [
                    "index.blocks.read_only_allow_delete" => null
                ],
                'mappings' => [
                    $type => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'created_at' => ['type' => 'text', 'fielddata' => true],
                        ]
                    ]
                ]
            ]
        ];
    }

    public function externalAccess()
    {
        return $this->hasMany('App\ChannelsExternalsAccess', 'channels_id', 'id')
            ->where('expire_at', '>=', date('Y-m-d H:i:s'))
            ->orderBy('start_at');
    }

    public function listRoles($roleKey = null)
    {
        if ($roleKey == null) {
            return config('rights.groups');
        } else {
            return config('rights.groups.' . $roleKey);
        }
    }
}
