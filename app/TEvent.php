<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\Searchable;

class TEvent extends Model
{
    /*
     * add Elasticsearch as observer
    */
    use Searchable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'events';
    protected $type = 'event';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('title', 'description');

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($event) {
            $event->medias()->detach();

            $event->shares()->get()->each(function ($share) {
                $share->delete();
            });

            $event->liked()->delete();

            $event->comments()->get()->each(function ($comment) {
                $comment->delete();
            });

            $event->actions()->get()->each(function ($action) {
                $action->delete();
            });

            $event->posts()->delete();

            $event->tags()->detach();
            $event->participantsUsers()->detach();

            // delete notifications
            $notification = Notif::where('type', '=', 'likeContent')
                ->where('parameter', 'LIKE', '%TEvent%element_id":"'.$event->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'comment')
                ->where('parameter', 'LIKE', '%TEvent%post_id":"'.$event->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'share')
                ->where('parameter', 'LIKE', '%Tevent%post_id":"'.$event->id.'"%')
                ->delete();
        });
    }


    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function getClassReflection()
    {
        $c = (new \ReflectionClass($this))->getShortName();
        return $c;
    }

    public function getType()
    {
        return $this->type;
    }

    public function instances()
    {
        return $this->hasMany('App\Instance', 'id', 'instances_id');
    }

    public function instance()
    {
        return $this->hasOne('App\Instance', 'id', 'instances_id');
    }

    public function getUrl()
    {
        /*
        $post = $this->posts()->first();
        $url = $post->author->getUrl();
        return $url.'/'.$post->id;
        */
        if ($this->newsfeedRef) {
            return $this->posts()->first()->author->getUrl().'/'.$this->newsfeedRef()->getResults()->id;
        }
        return $this->posts()->first()->author->getUrl();
    }

    public function getName()
    {
        return $this->title;
    }

    public function getNameDisplay()
    {
        return $this->description;
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

    /**
     * morph relation with links
     */
    public function links()
    {
        return $this->morphToMany('App\Link', 'linkable');
    }

    /**
     * morph relation with events author (profile)
     */
    public function author()
    {
        return $this->morphTo();
    }

    /**
     * morph relation when profile make actions
     */
    public function actions()
    {
        return $this->morphMany('App\NetframeAction', 'author');
    }

    /**
     * morph relation when event is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }

    /**
     * morph relation with news publications in newsfeeds
     */
    public function posts()
    {
        return $this->morphMany('App\NewsFeed', 'post');
    }

    /**
     * morph relation with news publications in newsfeeds
     */
    public function newsfeedRef()
    {
        return $this->morphOne('App\NewsFeed', 'post');
    }

    /**
     * morph relation with events comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post');
    }

    /**
     * morph relation with news comments for 2 lasts comments
     */
    public function lastComments()
    {
        $comments = $this->morphMany('App\Comment', 'post');
        if ($comments->count() > config('netframe')['number_comment']) {
            $skip = $comments->count() - config('netframe')['number_comment'];
            return $this->morphMany('App\Comment', 'post')->skip($skip)->take(config('netframe')['number_comment']);
        } else {
            return $this->morphMany('App\Comment', 'post')->take(config('netframe')['number_comment']);
        }
    }

    /**
     * morph relation with shares
     */
    public function shares()
    {
        return $this->morphMany('App\Share', 'post');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function medias()
    {
        return $this->belongsToMany('App\Media', 'events_has_medias', 'events_id', 'medias_id');
    }

    public function onlyImages()
    {
        foreach ($this->medias as $media) {
            if ($media->type != 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * return a resume of the post for description and sharing
     */
    public function resume()
    {
        return $this->title.' :: '.$this->description;
    }

    /**
     * relations with participants (users) of event
     */
    public function participantsUsers()
    {
        return $this->belongsToMany('App\User', 'events_has_friends', 'events_id', 'users_id')->withPivot('status');
    }

    /**
     * return true or false if event have user as participant
     * @param int $userId
     */
    public function hasParticipant($userId)
    {
        if ($this->participantsUsers()->where('users_id', '=', $userId)->first() != null) {
            return true;
        }
        return false;
    }

    /**
     * return nex events or last if there is no next
     * @param int $limit
     */
    public function nextOrLast($limit = 40)
    {
        $nowDay = Date('Y-m-d');
        $nextEvents = $this->where('instances_id', '=', session('instanceId'))
            ->where('date', '>=', $nowDay)
            ->whereConfidentiality(1)
            ->take($limit)
            ->with('posts');
        if ($nextEvents->count() != 0) {
            return $nextEvents->get();
        } else {
            return $this->where('instances_id', '=', session('instanceId'))
                ->whereConfidentiality(1)
                ->orderBy('date', 'desc')
                ->take($limit)
                ->with('posts')
                ->get();
        }
    }


    public function toReducedArray()
    {
        $profile = $this->posts()->first()->author;
        $array =  [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "content" => html_entity_decode($this->description),
            "confidentiality" => $this->confidentiality,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "instance" => $this->instances_id,
            "author_type" => $this->author_type,
            "author_id" => $this->author_id,
            "author" => $this->author->toReducedArray(),
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
            "profile_id" => sprintf('%s-%d', $profile->getType(), $profile->id),
            "profile_type" => $profile->getType(),
            "profile" => $profile->toReducedArray(),
            "url" => parse_url($this->getUrl())['path'],
        ];
        $array['tags'] = array_map(function ($tag) {
            return ["id" => $tag['id'], "name" => $tag['name']];
        }, $this->tags()->getResults()->toArray());
        return $array;
    }

    public static function mapping()
    {
        $index = self::first()->getSearchIndex();
        $type = self::first()->getSearchType();
        return [
            'index' => $index,
            'body' => [
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

    /*
     * convert dates from app timezone to utc timezone
     */
    public function convertToUtc()
    {
        if ($this->start_date != null) {
            $utcDate = \App\Helpers\DateHelper::convertToUTC($this->start_date);
            $this->time = $utcDate['time'];
            $this->start_date = $utcDate['datetime'];
        }

        if ($this->end_date != null) {
            $utcDate = \App\Helpers\DateHelper::convertToUTC($this->end_date);
            $this->time_end = $utcDate['time'];
            $this->end_date = $utcDate['datetime'];
        }
    }
}
