<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersReference extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_references';

    protected $type = 'user_reference';


    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * morph relation when reference is in netframe action
     */
    public function actions()
    {
        return $this->morphMany('App\NetframeAction', 'author');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function userCreate()
    {
        return $this->belongsTo('App\User', 'users_id_create', 'id');
    }

    /**
     * morph relation with tags
     */
    public function reference()
    {
        return $this->belongsTo('App\Tag', 'tags_id', 'id');
    }

    public function tags()
    {
        return $this->belongsTo('App\Tag', 'tags_id', 'id');
    }

    /**
     * morph relation with news is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }


    public function getType()
    {
        return $this->type;
    }
}
