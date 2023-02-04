<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buzz extends Model
{

    protected $table = "buzz";

    protected $fillable = [];

    public static function getTableName()
    {
        return with(new static())->getTable();
    }

    /**
     * morph relation with buzz profile
     */
    public function profile()
    {
        return $this->morphTo();
    }

    public static function topBuzz($column = 'day_score')
    {
        $buzzQuery = self::select($column)
        ->where($column, '>', 0)
        ->whereRaw('created_at = (select max(created_at) from buzz)')
        ->orderBy($column, 'desc')
        ->take(10)
        ->get();

        if (count($buzzQuery) > 0) {
            $buzzScore = $buzzQuery[count($buzzQuery)-1][$column];
        } else {
            $buzzScore = 0;
        }

        return $buzzScore;
    }
}
