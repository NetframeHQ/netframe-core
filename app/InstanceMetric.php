<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstanceMetric extends Model
{

    protected $table = "instance_metrics";

    protected $fillable = [];

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }
}
