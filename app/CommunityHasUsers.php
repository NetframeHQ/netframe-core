<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityHasUsers extends Model
{

    protected $table = "community_has_users";

    
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    //Data to persist into elasticsearch
    public function toReduceArray()
    {
        return [
            "community_id" => $this->community_id,
            "users_id" => $this->users_id,
        ];
    }
}
