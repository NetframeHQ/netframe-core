<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'interests';


    public static function getTableName()
    {
        return with(new static)->getTable();
    }



    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tag()
    {
        return $this->belongsTo('App\Tag');
    }
}
