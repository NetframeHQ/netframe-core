<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\Searchable;

class News extends Model
{
    /*
     * add Elasticsearch as observer
    */
    use Searchable;

    protected $table = "news";
    protected $type = "news";


    protected $fillable = [];

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($news) {
            $news->medias()->detach();

            $news->shares()->get()->each(function ($share) {
                $share->delete();
            });

            $news->liked()->delete();

            //$news->posts()->delete();

            $news->comments()->get()->each(function ($comment) {
                $comment->delete();
            });

            $news->tags()->detach();

            // delete notifications
            $notification = Notif::where('type', '=', 'likeContent')
                ->where('parameter', 'LIKE', '%News%element_id":"'.$news->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'comment')
                ->where('parameter', 'LIKE', '%News%post_id":"'.$news->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'share')
                ->where('parameter', 'LIKE', '%News%post_id":"'.$news->id.'"%')
                ->delete();
        });
    }

    public static function getTableName()
    {
        return with(new static())->getTable();
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

    protected $appends = ['postAuthor', 'mediasApi', 'formattedContent', 'links', 'onlyImage'];

    public function getPostAuthorAttribute()
    {
        return $this->author()->first();
    }

    public function getMediasApiAttribute()
    {
        return $this->medias()->get();
    }

    public function getLinksAttribute()
    {
        return $this->links()->get();
    }

    public function getOnlyImageAttribute()
    {
        return $this->onlyImages();
    }

    public function getFormattedContentAttribute()
    {
        return \App\Helpers\StringHelper::formatPostText($this->content);
    }

    /**
     * morph relation with news comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post')->whereNull('comments_id');
    }

    /**
     * morph relation with news comments for 2 lasts comments
     */
    public function lastComments()
    {
        $comments = $this->morphMany('App\Comment', 'post')->whereNull('comments_id');
        if ($comments->count() > config('netframe')['number_comment']) {
            $skip = $comments->count() - config('netframe')['number_comment'];
            return $this
                ->morphMany('App\Comment', 'post')
                ->whereNull('comments_id')
                ->skip($skip)
                ->take(config('netframe')['number_comment']);
        } else {
            return $this
                ->morphMany('App\Comment', 'post')
                ->whereNull('comments_id')
                ->take(config('netframe')['number_comment']);
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
        return $this->belongsToMany('App\Media', 'news_has_medias', 'news_id', 'medias_id');
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
    }

    public function toReducedArray()
    {
        $profile = $this->posts()->first()->author;
        $array = [
            "id" => $this->id,
            "active" => $this->active,
            "content" => html_entity_decode($this->content),
            "confidentiality" => $this->confidentiality,
            "instance" => $this->instances_id,
            "author_type" => $this->author_type,
            "author_id" => $this->author_id,
            "author" => $this->author->toReducedArray(),
            "media_id" => $this->media_id,
            "only_images" => empty($this->content) && $this->onlyImages(),
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

    public function getUrl()
    {
        if ($this->newsfeedRef) {
            return $this->posts()->first()->author->getUrl().'/'.$this->newsfeedRef->id;
        }
        return $this->posts()->first()->author->getUrl();
    }

    public function getNameDisplay()
    {
        $ref = $this->newsfeedRef;
        if ($ref) {
            return (isset($ref->true_author->firstname) ? ucfirst($ref->true_author->firstname) . ' ' : '')
                . $ref->true_author->name;
        }
        return $this->content;
    }

    public function getDescription()
    {
        return $this->content;
    }

    public function profileImage()
    {
        return $this->hasOne('App\Media', 'id', 'media_id');
    }
}
