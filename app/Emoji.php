<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emoji extends Model
{

    protected $table = "emojis";

    protected $fillable = [];

    public function groups()
    {
        return $this->belongsTo('App\EmojisGroup', 'emojis_groups_id', 'id');
    }
}
