<?php
    // Store result value input latitude & longitude
    $valueLatitude = (count($errors) > 0) ? \App\Helpers\InputHelper::get('latitude') : (($profile != null && $profile->latitude) ? $profile->latitude : '');
    $valueLongitude = (count($errors) > 0) ? \App\Helpers\InputHelper::get('longitude') : (($profile != null && $profile->longitude) ? $profile->longitude : '');
?>

<!--MAP -->
<label class="nf-form-cell nf-cell-full @if($errors->has('placeSearch')) nf-cell-error @endif @if(!$gdpr_agrement) d-none @endif">
    {{ Form::hidden('latitude', $valueLatitude, ['class'=>'input-latitude']) }}
    {{ Form::hidden('longitude', $valueLongitude, ['class'=>'input-longitude']) }}
    {{ Form::input('text',
        'placeSearch',
        (count($errors) > 0) ? \App\Helpers\InputHelper::get('placeSearch') : (($profile != null) ? $profile->location : ''),
        [
            'id' => 'pac-input-form',
            'class' => 'nf-form-input',
            'placeholder' => trans('map.searchPlaceHolder')
        ])
    }}
    <span class="nf-form-label">
        {{ trans('form.searchPlace.'.class_basename($profile)) }}
    </span>
    {!! $errors->first('placeSearch', '<div class="nf-form-feedback">:message</div>') !!}
    <div class="nf-form-cell-fx"></div>
</label>

<div id="mini-map-form"></div>

@section('javascripts')
    @parent
<script>
$(document).ready(function() {
    //initializeMiniMapForm();
});
</script>
@stop

