<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = "roles";

    protected $fillable = [];

    public function getSelectList()
    {
        $list = $this->orderBy('id')->pluck('name', 'id')->toArray();
        foreach ($list as $key => $elem) {
            $list[$key] = trans('profiles.roles.'.$elem);
        }
        $list[0] = '';
        ksort($list);
        return $list;
    }
}
