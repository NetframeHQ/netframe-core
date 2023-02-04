<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boarding extends Model
{

    protected $table = "boarding";

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public static function generateFirstKey()
    {
        $key1 = rand(100, 999);
        $key2 = rand(100, 999);
        $key = $key1.'-'.$key2;
        return $key;
    }
}
