<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelsExternalsAccess extends Model
{
    protected $table = 'channels_externals_access';

    public function channel()
    {
        return $this->belongsTo('App\Channel', 'channels_id', 'id');
    }

    public function isActive()
    {
        return ($this->start_at < date('Y-m-d H:i:s') && $this->expireat > date('Y-m-d H:i:s'));
    }

    public function getUrl()
    {
        $instance = Instance::find(session('instanceId'));
        return $instance->getUrl().'/gotovisio/'.session('instanceId').'/'.$this->channel->id.'/'.$this->slug;
    }
}
