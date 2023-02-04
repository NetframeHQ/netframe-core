<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use CacheQueryBuilder;

    protected $table = "apps";

    public function instances()
    {
        return $this->belongsToMany('App\Instance', 'instances_has_apps', 'instances_id', 'apps_id');
    }
}
