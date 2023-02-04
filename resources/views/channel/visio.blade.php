@extends('layouts.master')

@section('content')
    <iframe src="{{ $iframeUrl }}" allow="camera *; microphone *;" name="live-stage" id="live-stage" style="top:0; bottom:0; left:0; right:0; position: absolute; width: 100%; height:100%; border:none;">
    </iframe>
@stop

@section('javascripts')
@parent
<script>

window.onbeforeunload = function() {
    console.log('quit stage');
    updateLiveMembers({{$channel->id}}, -1);
}

Echo.join('livestage-{{$channel->id}}')
    .here((users) => {
        this.users = users;
        this.usersCount = users.length;
        updateLiveMembers({{$channel->id}}, +1);
    })
    .joining((user) => {
        this.users.push(user);
        this.usersCount = this.usersCount+1;
        updateLiveMembers({{$channel->id}}, +1);
    })
    .leaving((user) => {
        this.usersCount = this.usersCount-1;
        //updateLiveMembers({{$channel->id}}, -1);
    });

function updateLiveMembers(channelId, members)
{
    var data = {
            channelId: channelId,
            members: members
    };
    console.log('livechannel');

    $.ajax({
        url: laroute.route('channels.livechat.update.members'),
        data: data,
        type: "POST",
        success: function( data ) {

        },
        error: function(textStatus, errorThrown) {
            //console.log(textStatus);
        }
    });
}
</script>
@stop