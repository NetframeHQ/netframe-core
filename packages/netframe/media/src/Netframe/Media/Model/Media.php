<?php
namespace Netframe\Media\Model;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;

class Media extends Model
{
    use CacheQueryBuilder;

    protected $table = "medias";

    protected $fillable = ["encoded"];

    const TYPE_IMAGE = 0;
    const TYPE_VIDEO = 1;
    const TYPE_AUDIO = 2;
    const TYPE_DOCUMENT = 3;
    const TYPE_ARCHIVE = 4;
    const TYPE_APPLICATION = 5;
    const TYPE_SCRIPT = 6;
    const TYPE_OTHER = 7;
    const TYPE_FONT = 8;

    public function actions()
    {
        return $this->morphMany('App\NetframeAction', 'author');
    }

    /**
     * Checks if the media is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->encoded == 1;
    }

    public function isTypeDisplay()
    {
        $not_display_types = [
            self::TYPE_DOCUMENT,
            self::TYPE_ARCHIVE,
            self::TYPE_APPLICATION,
            self::TYPE_SCRIPT,
            self::TYPE_OTHER,
            self::TYPE_FONT
        ];

        if (in_array($this->type, $not_display_types)) {
            return false;
        } else {
            return true;
        }
    }

    public function isDocument()
    {
        return $this->type == self::TYPE_DOCUMENT;
    }

    public function hasEncodedMedias()
    {
        foreach ($this->medias as $media) {
            if ($media->encoded == 1) {
                return true;
            }
        }

        return false;
    }

    public function isInstantBookmarkedByCurrentUser()
    {

        $user = auth()->guard('web')->user();

        $items = \App\Playlist::where('playlists.author_id', '=', $user->id)
            ->where('playlists.author_type', '=', 'User')
            ->where('playlists.instant_playlist', '=', 1)

            ->join('playlists_items', 'playlists_items.playlists_id', '=', 'playlists.id')
            ->where('medias_id', '=', $this->id)
            ->first();
        /*
        $items = PlaylistItem::where('users_id', '=', $user->id)
        ->where('medias_id', '=', $this->id)
        ->first();
        */

        return count($items) > 0;
    }

    public function languages()
    {
        return $this->belongsTo('App\Language', 'language', 'iso_639_2')->where('lang', '=', \Lang::locale());
    }

    public function getUrl()
    {
        if ($this->platform == 'local') {
            return url()->route('media_download', $this->id);
        } else {
            return url()->route('media_download', $this->id).'?thumb=1';
        }
    }

    public function getUrlShare()
    {
        return url()->route('urlto.media', ['fileName' => $this->file_name , 'mediaId' => $this->id]);
    }

    public function author()
    {
        $profiles = config('media.owner_relation');

        foreach ($profiles as $model => $relTable) {
            $owner = $this
                ->belongsToMany('App\\'.$model, $relTable.'_has_medias', 'medias_id', $relTable.'_id')
                ->withPivot(['profile_image', 'medias_folders_id']);

            if ($owner->get()->first() != null) {
                return $owner;
            }
        }

        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    /*
     * morph relation with  like
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }

    /**
     * morph relation with media comments
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
     * relation with events
     */
    public function events()
    {
        return $this->belongsToMany('App\TEvent', 'events_has_medias', 'medias_id', 'events_id');
    }

    /**
     * relation with news
     */
    public function news()
    {
        return $this->belongsToMany('App\News', 'news_has_medias', 'medias_id', 'news_id');
    }

    /**
     * relation with offers
     */
    public function offers()
    {
        return $this->belongsToMany('App\Offer', 'offers_has_medias', 'medias_id', 'offers_id');
    }

    public function getBaseName()
    {
        return str_replace('.'.$this->getExtension(), '', $this->name);
    }

    public function getExtension()
    {
        return ltrim(strstr($this->name, '.'), '.');
    }
}
