<html>
<head></head>
<body>
@php

@endphp


{{--<form action="{{ $glowbl['url'] }}" method="post" enctype="application/x-www-form-urlencoded" id="go-to-stage">
    <input type="hidden" name="lti_version" value="LTI-1p0">
    <input type="hidden" name="lti_message_type" value="basic-lti-launch-request">
    <input type="hidden" name="launch_presentation_locale" value="{{ $glowbl['user']->lang }}">
    <input type="hidden" name="lis_person_name_full" value="{{ $glowbl['fullname'] }}">
    <input type="hidden" name="lis_person_name_given" value="{{ auth()->guard('web')->user()->firstname }}">
    <input type="hidden" name="lis_person_name_family" value="{{ auth()->guard('web')->user()->name }}">
    <input type="hidden" name="oauth_consumer_key" value="{{ $glowbl['consumerKey'] }}">
    <input type="hidden" name="oauth_signature_method" value="HMAC-SHA1">
    <input type="hidden" name="oauth_signature" value="{{ $glowbl['signature'] }}">
    <input type="hidden" name="resource_link_id" value="{{$channel->id}}">
    <input type="hidden" name="resource_link_title" value="{{$channel->getNameDisplay()}}">
    <input type="hidden" name="roles" value="Administrator">
    <input type="hidden" name="user_id" value="{{ $glowbl['user']->id }}">
</form>
--}}

<form id="go-to-stage" name="go-to-stage" method="POST" action="{{ $launch_url }}">
@foreach ($launch_data as $k => $v )
    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
@endforeach
    <input type="hidden" name="oauth_signature" value="{{ $signature }}">
</form>


{{ HTML::script('assets/js/jquery.min.js') }}
<script>
(function($){
    var fromStage = $('#go-to-stage');
    $('#go-to-stage').submit();
})(jQuery);
</script>
</body>
</html>