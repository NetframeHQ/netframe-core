<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifMail extends Model
{

    protected $table = "notif_mails";

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }
}
