<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersGroup extends Model
{

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'users_groups_has_users', 'users_groups_id', 'users_id')
            ->orderBy('name');
    }

    public function houses()
    {
        return $this->morphedByMany('App\House', 'groups_profiles')
            ->orderBy('name');
    }

    public function communities()
    {
        return $this->morphedByMany('App\Community', 'groups_profiles')
        ->orderBy('name');
    }

    public function projects()
    {
        return $this->morphedByMany('App\Project', 'groups_profiles')
            ->orderBy('title');
    }

    public function channels()
    {
        return $this->morphedByMany('App\Channel', 'groups_profiles')
            ->orderBy('name');
    }
}
