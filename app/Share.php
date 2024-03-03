<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{

    protected $table = "shares";

    protected $fillable = [];


    public static function boot()
    {
        parent::boot();

        self::deleting(function ($share) {
            $share->medias()->detach();

            $share->liked()->delete();

            $share->comments()->get()->each(function ($comment) {
                $comment->delete();
            });

            // delete notifications
            $notification = Notif::where('type', '=', 'likedContent')
                ->where('parameter', 'LIKE', '%Share%element_id":"'.$share->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'comment')
                ->where('parameter', 'LIKE', '%Share%post_id":"'.$share->id.'"%')
                ->delete();
        });
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * morph relation with news author (profile)
     */
    public function author()
    {
        return $this->morphTo();
    }

    /**
     * morph relation with post types : News, TEvent, Share, NetframeAction
     */
    public function post()
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

    protected $appends = ['postAuthor', 'mediasApi', 'formattedContent', 'links','isShare'];

    public function getIsShareAttribute()
    {
        return 1;
    }

    public function getPostAuthorAttribute()
    {
        return $this->author()->first();
    }

    public function getMediasApiAttribute()
    {
        if (class_basename($this->post) == 'Media') {
            return [$this->post];
        } else {
            return $this->post->medias()->get();
        }
    }

    public function getLinksAttribute()
    {
        if (in_array(class_basename($this->post), ['News', 'TEvent', 'Offer'])) {
            return $this->post->links()->get();
        }
        return [];
    }

    public function getFormattedContentAttribute()
    {
        $headShare = '';
        if (get_class($this->post) == 'App\Media') {
            $headShare = \HTML::thumbImage(
                $this->post->author->first()->profile_media_id,
                60,
                60,
                [],
                $this->post->author->first()->getType()
            );
        }

        $headShare .= trans('page.share.'.class_basename($this->post).'SharedFrom');

        if (get_class($this->post) != 'App\Media') {
            $headShare .= ' <a href="'. $this->post->posts()->first()->author->getUrl() . '">' .
                $this->post->posts()->first()->author->getNameDisplay() .
                '</a>';
        } else {
            $headShare .= ' <a href="' . $this->post->author->first()->getUrl() . '">' .
                 $this->post->author->first()->getNameDisplay() .
                '</a>';
        }

        if (in_array(class_basename($this->post), ['TEvent', 'Offer', 'News'])) {
            if ($this->post->posts[0]->true_author != $this->post->posts[0]->author &&
                class_basename($this->post->posts[0]->author) != 'Channel') {
                $author = $this->post->posts[0]->author;
            } else {
                $author = $this->post->posts[0]->true_author;
            }
            $initialPostDate = \App\Helpers\DateHelper::feedDate(
                $this->post->posts[0]->created_at,
                $this->post->posts[0]->updated_at
            );
        } elseif (get_class($this->post) == 'App\Media') {
            $author = $this->post->author->first()->author;
            $initialPostDate = \App\Helpers\DateHelper::feedDate(
                $this->post->created_at,
                $this->post->updated_at
            );
        }

        if (class_basename($this->post) == 'TEvent') {
            $sharedContent = view(
                'channel.partials.content.event',
                [
                    'post' => $this->post,
                    'author' => $author,
                    'date' => $initialPostDate
                ]
            );
        } elseif (class_basename($this->post) == 'Offer') {
            $sharedContent = view(
                'channel.partials.content.offer',
                [
                    'post' => $this->post,
                    'author' => $author,
                    'date' => $initialPostDate
                ]
            );
        } elseif (in_array(class_basename($this->post), ['User', 'House', 'Community', 'Project'])) {
            $sharedContent = view('channel.partials.content.profile', ['profile' => $this->post]);
        } elseif (class_basename($this->post) == 'News') {
            $sharedContent = view(
                'channel.partials.content.news',
                [
                    'post' => $this->post,
                    'author' => $author,
                    'date' => $initialPostDate
                ]
            );
        } elseif (class_basename($this->post) == 'Media') {
            $sharedContent = view(
                'channel.partials.content.media',
                [
                    'post' => $this->post,
                    'author' => $author,
                    'date' => $initialPostDate
                ]
            );
        } else {
            $sharedContent = \App\Helpers\StringHelper::formatPostText($this->post->content);
        }

        return '<p>' .
            $this->content .
            '</p><div class="shared-content">' .
            $sharedContent .
            '</div>';
    }

    /**
     * morph relation with news comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post');
    }

    /**
     * return a resume of the post for description and sharing
     */
    public function resume()
    {
        return $this->content;
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

    public function newsFeed()
    {
        return $this->hasOne('App\NewsFeed', 'id', 'news_feed_id');
    }

    public function media()
    {
        return $this->hasOne('App\Media', 'id', 'media_id');
    }

    /**
     * morph relation with medias
     */
    public function medias()
    {
        return $this->morphTo();
    }

    public function onlyImages()
    {
        return false;
    }

    public function getUrl()
    {
        if ($this->newsfeedRef) {
            return $this->author->getUrl().'/'.$this->newsfeedRef->id;
        }
            return $this->author->getUrl();
    }
}
