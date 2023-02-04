<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingLine extends Model
{

    protected $table = "billing_lines";

    protected $fillable = [];

    public function billing()
    {
        return $this->belongsTo('App\Billing', 'billings_id', 'id');
    }

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'billings_instances_id', 'id');
    }
}
