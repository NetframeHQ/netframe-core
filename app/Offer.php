<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\Searchable;

class Offer extends Model
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
    protected $table = "offers";
    protected $type = "offer";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($offer) {
            $offer->medias()->detach();

            $offer->shares()->get()->each(function ($share) {
                $share->delete();
            });

            $offer->liked()->delete();

            $offer->posts()->delete();

            $offer->comments()->get()->each(function ($comment) {
                $comment->delete();
            });

            $offer->tags()->detach();

            // delete notifications
            $notification = Notif::where('type', '=', 'likeContent')
                ->where('parameter', 'LIKE', '%Offer%element_id":"' . $offer->id . '"%')
                ->delete();
            $notification = Notif::where('type', '=', 'comment')
                ->where('parameter', 'LIKE', '%Offer%post_id":"' . $offer->id . '"%')
                ->delete();
            $notification = Notif::where('type', '=', 'share')
                ->where('parameter', 'LIKE', '%Offer%post_id":"' . $offer->id . '"%')
                ->delete();
        });
    }

    public function __construct()
    {
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
     * morph relation with news author (profile)
     */
    public function author()
    {
        return $this->morphTo();
    }

    /**
     * morph relation with news is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }

    /**
     * morph relation with publications in newsfeeds
     */
    public function posts()
    {
        return $this->morphMany('App\NewsFeed', 'post');
    }

    public function newsfeedRef()
    {
        return $this->morphOne('App\NewsFeed', 'post');
    }

    /**
     * morph relation with news comments
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

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function medias()
    {
        return $this->belongsToMany('App\Media', 'offers_has_medias', 'offers_id', 'medias_id');
    }

    public function mediasIds()
    {
        $mediasList = $this->medias;
        $mediasIds = array();
        foreach ($mediasList as $media) {
            $mediasIds[] = $media->id;
        }

        return $mediasIds;
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
        return $this->name.' :: '.$this->content;
    }

    public function toReducedArray()
    {
        $array = [
            "id" => $this->id,
            "name" => $this->name,
            "content" => $this->content,
            "instance" => $this->instances_id,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "author_type" => $this->author_type,
            "author_id" => $this->author_id,
            "author" => $this->author->toReducedArray(),
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
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

    public function getUrl()
    {
        if ($this->newsfeedRef) {
            return $this->posts()->first()->author->getUrl().'/'.$this->newsfeedRef()->getResults()->id;
        }
        return $this->posts()->first()->author->getUrl();
    }

    public function getNameDisplay()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->content;
    }
}
