<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;

class UserParameter extends Model
{
    use CacheQueryBuilder;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_parameters';


    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
