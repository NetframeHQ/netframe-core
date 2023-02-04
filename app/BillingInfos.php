<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class BillingInfos extends Model
{

    use Encryptable;

    protected $table = "billing_infos";

    protected $fillable = ['instances_id', 'type'];

    protected $encryptable = ['value'];

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }
}
