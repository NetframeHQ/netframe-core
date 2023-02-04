<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friends extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'friends';

    protected $guarded = array(
        'id'
    );

    public static function getTableName()
    {
        return with(new static())->getTable();
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    public function friends()
    {
        // return $this->hasOne('User', 'id', 'users_id');
        return $this->hasMany('App\User', 'id', 'friends_id');
    }

    public static function countFriends()
    {
        $query = Friends::where(function ($where) {
            $where->where('users_id', '=', auth()->guard('web')->user()->id)
                  ->orWhere('friends_id', '=', auth()->guard('web')->user()->id);
        })
            ->where('blacklist', '=', '0')
            ->where('status', '=', '1')
            ->count();
        return $query;
    }

    public static function relation($user_foreign)
    {
        return Friends::orWhere(function ($where) use ($user_foreign) {
                $where->where('users_id', '=', auth()->guard('web')->user()->id)
                      ->where('friends_id', '=', $user_foreign);
        })
                ->orWhere(function ($where) use ($user_foreign) {
                    $where->where('friends_id', '=', auth()->guard('web')->user()->id)
                          ->where('users_id', '=', $user_foreign);
                })->first();
    }

    public static function checkFriend($user_from, $user_foreign)
    {
        $testFriend =  Friends::orWhere(function ($where) use ($user_from, $user_foreign) {
            $where->where('users_id', '=', $user_from)
            ->where('friends_id', '=', $user_foreign);
        })
        ->orWhere(function ($where) use ($user_from, $user_foreign) {
            $where->where('friends_id', '=', $user_from)
            ->where('users_id', '=', $user_foreign);
        })->first();

        return ($testFriend == null) ? false : true;
    }

    public static function relationIds($current_user, $user_foreign)
    {
        return Friends::orWhere(function ($where) use ($user_foreign, $current_user) {
            $where->where('users_id', '=', $current_user)
            ->where('friends_id', '=', $user_foreign);
        })
        ->orWhere(function ($where) use ($user_foreign, $current_user) {
            $where->where('friends_id', '=', $current_user)
            ->where('users_id', '=', $user_foreign);
        })->first();
    }

    public function findByProfileId($blacklist, $status, $profile_id)
    {
        $query = Friends::where([
            'blacklist' => $blacklist,
            'status' => $status,
            'users_id' => auth()->guard('web')->user()->id,
            'friends_id' => $profile_id
        ])->orWhere(function ($w) use ($blacklist, $status, $profile_id) {
            $w->where([
                'blacklist' => $blacklist,
                'status' => $status,
                'users_id' => $profile_id,
                'friends_id' => auth()->guard('web')->user()->id
            ]);
        });


        return $query;
    }

    public static function friendListId()
    {
        $friends = Friends::select('users_id', 'friends_id')
            ->where('users_id', '=', auth()->guard('web')->user()->id)
            ->orWhere('friends_id', '=', auth()->guard('web')->user()->id)
            ->get();

        $friendsId = [];
        foreach ($friends as $friend) {
            $friendsId[] = ($friend->users_id != auth()->guard('web')->user()->id)
                ? $friend->users_id
                : $friend->friends_id;
        }

        return $friendsId;
    }
}
