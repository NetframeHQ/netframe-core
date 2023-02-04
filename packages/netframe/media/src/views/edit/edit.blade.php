@extends('layouts.master')

@section('stylesheets')
    @parent

    <link href="{{ asset('packages/netframe/media/vendor/videojs/video-js.min.css') }}" rel="stylesheet">
@overwrite

@section('content')
<div class="col-lg-4 col-lg-offset-4">
    <h1 class="text-center">{{ trans('media::messages.edit_media') }}</h1>

    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
      {{ session('success') }}
    </div>
    @endif

    @include('media::player.player', array('media' => $media, 'attributes' => $player_attributes))

    {{ Form::model($media, array('route' => array('media_edit', $media->id), 'name' => 'media_edit')) }}

        <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
            {{ Form::label('name') }}
            {{ Form::text('name', null, array('class' => 'form-control')) }}

            @if ($errors->has('name'))
                <span class="help-block">{{ $errors->first('name') }}</span>
            @endif
        </div>

        <button type="submit" class="btn btn-success">{{ trans('media::messages.save') }}</button>

    {{ Form::close() }}

    {{ Form::open(array('route' => array('media_delete', $media->id))) }}
        <button type="submit" class="btn btn-danger">{{ trans('media::messages.delete') }}</button>
    {{ Form::close() }}
</div>
@stop

@section('javascripts')
    @parent

    <script src="{{ asset('packages/netframe/media/vendor/audiojs/audio.min.js') }}"></script>
    <script src="{{ asset('packages/netframe/media/vendor/videojs/video.js') }}"></script>
    <script>videojs.options.flash.swf = "{{ asset('packages/netframe/media/vendor/videojs/video-js.swf') }}"</script>

    <script type="text/javascript">
      audiojs.events.ready(function() {
        var as = audiojs.createAll();
      });
    </script>
@stop
