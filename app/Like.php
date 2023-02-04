<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use CacheQueryBuilder;

    protected $table = "likes";

    protected $fillable = [];


    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    /**
     * morph relation with user and profile who like item
     */
    public function liker()
    {
        return $this->morphTo();
    }

    /**
     * morph relation with item liked by profile or user
     */
    public function liked()
    {
        return $this->morphTo();
    }

    /**
     * Check if like already exist
     *
     * @return boolean true / false
     */
    public function likeExist($idUser, $liker_id, $liker_type, $liked_id, $liked_type)
    {
        $query = \DB::table($this->table)->where('users_id', '=', $idUser)
            ->where('liker_id', '=', $liker_id)
            ->where('liker_type', '=', $liker_type)
            ->where('liked_id', '=', $liked_id)
            ->where('liked_type', '=', $liked_type)
            ->exists();

        return $query;
    }


    /**
     *
     * @param (array/object) $data id_foreign or type_foreign
     * @return (object) query result row
     */
    public static function existing($liked_id, $liked_type)
    {
        $query = static::where(array(
            'users_id' => auth()->guard('web')->user()->id,
            'liked_id' => $liked_id,
            'liked_type' => $liked_type
        ))->first();

        return $query;
    }

    public static function isLiked($data)
    {
        $data = (object) $data;

        $query = static::where(array(
            'users_id' => auth()->guard('web')->user()->id,
            'liked_id' => $data->liked_id,
            'liked_type' => $data->liked_type
        ))->first();

        return $query != null;
    }


    /*
     *  Get Like profile if existe
     *
     */
    public static function getProfile($idLikeForeign, $typeLikeforeign)
    {

        if (auth()->guard('web')->check()) {
            $query = \DB::table(static::getTableName())->where([
                'users_id' => auth()->guard('web')->user()->id,
                'liked_id' => $idLikeForeign,
                'liked_type' => studly_case($typeLikeforeign)
            ])->first();
        } else {
            $query = null;
        }

        return $query;
    }

    public function emoji()
    {
        return $this->hasOne('App\Emoji', 'id', 'emojis_id');
    }
}
