<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;

class InstanceParameter extends Model
{
    use CacheQueryBuilder;

    protected $table = "instance_parameters";

    protected $fillable = [];

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }
}
