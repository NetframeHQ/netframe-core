<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class EmojisGroup extends Model
{
    use CacheQueryBuilder;

    protected $table = "emojis_groups";

    protected $fillable = [];

    public function emojis()
    {
        return $this->hasMany('App\Emoji', 'emojis_groups_id', 'id')->orderBy('order');
    }
}
