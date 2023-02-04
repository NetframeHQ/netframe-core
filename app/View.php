<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{

    protected $table = "views";

    const TYPE_CREATE = 0;
    const TYPE_OPEN = 1;
    const TYPE_EDIT = 2;
    const TYPE_DOWNLOAD = 3;
    const TYPE_REPLACE = 4;

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id')
        ->whereHas('instances', function ($wI) {
            $wI->where('id', '=', session('instanceId'));
        });
    }
}
