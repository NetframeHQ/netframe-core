<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'playlists';

    public function getUrl()
    {
        return url()->route('playlist_show', ['id' => $this->id]);
    }

    /**
     * morph relation to playlist owner (users and profiles)
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

    /**
     * morph relation with news comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post');
    }

    /**
     * morph relation with shares
     */
    public function shares()
    {
        return $this->morphMany('App\Share', 'post');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\PlaylistItem', 'playlists_id');
    }

    public function medias()
    {
        return $this->hasMany('App\PlaylistItem', 'playlists_id');
    }

    public function itemsNf()
    {
        return $this->hasMany('App\PlaylistItem', 'playlists_id')->take(5);
    }

    public function delete()
    {
        \PlaylistItem::where('playlists_id', '=', $this->id)
            ->delete();

        return parent::delete();
    }

    /**
     * return a resume of the post for description and sharing
     */
    public function resume()
    {
        return trans('playlist.playlist').' :: '.$this->name.', '.$this->description;
    }
}
