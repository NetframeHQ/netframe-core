<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    protected $table = "subscriptions";

    protected $fillable = [];


    public static function getTableName()
    {
        return with(new static)->getTable();
    }


    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id')
            ->where('instances_id', '=', session('instanceId'));
    }

    public function profile()
    {
        return $this->morphTo();
    }

    /**
     *
     * @param (array) $data id_foreign & type_foreign
     * @return (object) query result row
     */
    public static function existing($profile_id, $profile_type, $users_id = null)
    {
        if ($users_id == null) {
            $users_id = auth()->guard('web')->user()->id;
        }
        $query = static::where([
                'instances_id' => session('instanceId'),
                'users_id' => $users_id,
                'profile_id' => $profile_id,
                'profile_type' => "App\\".$profile_type
            ])->first();

        return $query;
    }

    public static function checkSubscribe($profile_id, $profile_type)
    {
        if (auth()->guard('web')->check()) {
            $items = Subscription::where('users_id', '=', auth()->guard('web')->user()->id)
                ->where('instances_id', '=', session('instanceId'))
                ->where('profile_type', '=', $profile_type)
                ->where('profile_id', '=', $profile_id)
                ->first();
            if ($items != null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
    public function insertByUsersIdAndIdForeignAndTypeForeign($users_id, $id_foreign, $type_foreign)
    {
        $query = DB::table('subscriptions')->insert(array(
            'users_id' => $users_id,
            'id_foreign' => $id_foreign,
            'type_foreign' => $type_foreign,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ));
    }
    */

    public function findByUsersIDAndIdForeignAndTypeForeign($users_id, $id_foreign, $type_foreign)
    {
        $query = Subscription::where('instances_id', '=', session('instanceId'))
            ->where([
                'users_id' => $users_id,
                'profile_id' => $id_foreign,
                'profile_type' => studly_case($type_foreign)
            ]);
    }
}
