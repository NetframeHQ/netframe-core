<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{

    protected $table = "billings";

    protected $fillable = [];

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function billingLines()
    {
        return $this->hasMany('App\BillingLine', 'billings_id', 'id');
    }
}
