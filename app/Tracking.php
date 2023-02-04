<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trackings';


    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
