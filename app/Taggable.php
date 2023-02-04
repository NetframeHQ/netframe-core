<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Observers\Searchable;

class Taggable extends Model
{
    use Searchable;

    public $timestamps = false;
    public $incrementing = false;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'taggables';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function getType()
    {
        return $this->table;
    }
}
