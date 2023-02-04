<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
    /**
     * Cache the profile.
     *
     * @var PlaylistItemProfile
     */
    private $profile;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'playlists_items';

    /**
     * morph relation to profile playlisted
     */
    public function profile()
    {
        return $this->morphTo();
    }

    public function media()
    {
        return $this->hasOne('App\Media', 'id', 'medias_id');
    }

    public function playlist()
    {
        return $this->hasOne('App\Playlist', 'id', 'playlists_id');
    }

    public function getUrl()
    {
        if ($this->medias_id != null && $this->media->platform == 'local') {
            return url()->route('media_download', $this->medias_id);
        } elseif ($this->medias_id != null) {
                return url()->route('media_download', $this->medias_id).'?thumb=1';
        } else {
            return '';
        }
    }

    /**
     * Gets the profile.
     *
     * @return PlaylistItemProfile
     */

    /**
     * Get all items playlisted for a user.
     *
     *
     * return tab with playlisted items
     */
    public static function getAllUserItems()
    {
        $user = auth()->guard('web')->user();
        if (!empty($user)) {
            $query = \DB::table('playlists_items')
                ->select('playlists_items.profile_type', 'playlists_items.profile_id');
            $query->leftJoin('playlists', function ($join) {
                $join->on('playlists.id', '=', 'playlists_items.playlists_id');
            });
            $query->where('playlists.instances_id', '=', session('instanceId'));
            $query->where('playlists.users_id', '=', $user->id);
            $userItems = $query->get();
            $arrayItems = array();
            foreach ($userItems as $item) {
                $arrayItems[strtolower($item->profile_type)][$item->profile_id] = 1;
            }

            return $arrayItems;
        } else {
            return false;
        }
    }
}
