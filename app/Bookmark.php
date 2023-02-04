<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{

    protected $table = "bookmarks";

    protected $fillable = [];

    public static function getTableName()
    {
        return with(new static())->getTable();
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'projects_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }
}
