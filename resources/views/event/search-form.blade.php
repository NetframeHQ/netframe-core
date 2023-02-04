{{ Form::open(array('id' => 'eventsForm', 'name' => 'eventsForm')) }}
<div class="well well-sm clearfix">
    <div class="row visible-xs">
        <div class="form-group col-md-12">
            <a href="{{ url()->route('event_edit') }}" class="btn btn-border-default float-right" data-toggle="modal" data-target="#modal-ajax">
                <span class="icon ticon-publish"></span>
                {{ trans('netframe.createEvent') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-9 col-xs-12">
            {{ Form::label('keywords', trans('project.searchKeywords')) }}
            {{ Form::text('keywords', '', array('class' => 'form-control')) }}
        </div>
        <div class="col-md-3 hidden-xs">
            <a href="{{ url()->route('event_edit') }}" class="btn btn-border-default float-right" data-toggle="modal" data-target="#modal-ajax">
                {{ trans('netframe.createEvent') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="place-search">
                {{ Form::label('placeSearch', trans('search.whereAreYou')) }}
                {{ Form::input('text', 'placeSearch', '', array('id' => 'pac-input', 'class' => 'form-control', 'placeholder' => trans('map.searchPlaceHolder'))) }}
                {{ Form::input('hidden', 'latitude', '', ['id' => 'latitude'] ) }}
                {{ Form::input('hidden', 'longitude', '', ['id' => 'longitude'] ) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('distanceSlider', trans('search.distance')) }}<br />
                <input name="distance" class="distance-selector" data-slider-id='distanceSlider' type="text" style="width: 80%; margin-bottom: 24px;" data-slider-value="{{ $searchDistance }}" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-right">
            {{ Form::submit(trans('project.search'), array('class' => 'btn btn-primary')) }}
        </div>
    </div>
</div>
{{ Form::close() }}

