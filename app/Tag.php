<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tags';

    public $functionsRelations = [
        'users_references' => 'UsersReference',
        'houses' => 'House',
        'communities' => 'Community',
        'projects' => 'Project',
        'news' => 'News',
        'tasktables' => 'TaskTable',
        'events' => 'TEvent',
        'offers' => 'Offer',
        'medias' => 'Media',
        'channels' => 'Channel',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function users_references()
    {
        return $this->hasMany('App\UsersReference', 'tags_id');
    }

    public function houses()
    {
        return $this->morphedByMany('App\House', 'taggable');
    }

    public function communities()
    {
        return $this->morphedByMany('App\Community', 'taggable');
    }

    public function projects()
    {
        return $this->morphedByMany('App\Project', 'taggable');
    }

    public function news()
    {
        return $this->morphedByMany('App\News', 'taggable');
    }

    public function taskTables()
    {
        return $this->morphedByMany('App\TaskTable', 'taggable');
    }

    public function offers()
    {
        return $this->morphedByMany('App\Offer', 'taggable');
    }

    public function events()
    {
        return $this->morphedByMany('App\TEvent', 'taggable');
    }

    public function channels()
    {
        return $this->morphedByMany('App\Channel', 'taggable');
    }

    public function medias()
    {
        return $this->morphedByMany('App\Media', 'taggable');
    }

    public function interests()
    {
        return $this->hasMany('App\Interest', 'tags_id', 'id');
    }

    public function toSelect2()
    {
        return 'test';
    }

    public function getType()
    {
        return $this->table;
    }
}
