<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';

    public function news()
    {
        return $this->morphedByMany('App\News', 'linkable');
    }

    public function offers()
    {
        return $this->morphedByMany('App\Offer', 'linkable');
    }

    public function events()
    {
        return $this->morphedByMany('App\TEvent', 'linkable');
    }
}
