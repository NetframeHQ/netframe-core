<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NetframeAction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'netframe_actions';

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($nfaction) {
            $nfaction->medias()->detach();

            $nfaction->shares()->get()->each(function ($share) {
                $share->delete();
            });

            $nfaction->liked()->delete();

            $nfaction->comments()->get()->each(function ($comment) {
                $comment->delete();
            });

            // delete notifications
            $notification = Notif::where('type', '=', 'likedContent')
                ->where('parameter', 'LIKE', '%NetframeAction%element_id":"'.$nfaction->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'comment')
                ->where('parameter', 'LIKE', '%NetframeAction%post_id":"'.$nfaction->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'share')
                ->where('parameter', 'LIKE', '%NetframeAction%post_id":"'.$nfaction->id.'"%')
                ->delete();
        });
    }

    public function getUrl()
    {
        if ($this->newsfeedRef) {
            return $this->posts()->first()->author->getUrl().'/'.$this->newsfeedRef->id;
        }
            return $this->posts()->first()->author->getUrl();
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function author()
    {
        return $this->morphTo();
    }

    public function post()
    {
        return $this->morphMany('App\NewsFeed', 'post');
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
     * morph relation with news comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post')->whereNull('comments_id');
    }

    /**
     * morph relation with medias
     */
    public function medias()
    {
        return $this->morphTo();
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
     * morph relation with news is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }


    /**
     * morph relation with shares
     */
    public function shares()
    {
        return $this->morphMany('App\Share', 'post');
    }

    public function onlyImages()
    {
        return false;
    }
}
