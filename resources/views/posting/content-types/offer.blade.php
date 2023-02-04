<div class="panel-post-input-head">
    <div class="form-group panel-post-name">
        {{ Form::label('name', trans('offer.name'), ['class' => 'sr-only'] ) }}
        {{ Form::text('name', $post->name, ['class' => 'form-control '.(($errors->has('name')) ? 'is-invalid' : ''), 'placeholder' => trans('offer.name'), 'autocomplete' => 'off']) }}

        @if ($errors->has('name'))
            <span class="invalid-feedback">{!! $errors->first('name') !!}</span>
        @endif
    </div>
    <div class="form-group panel-post-place">
        @include('location.minimap-form', ['profile' => $post, 'noSearchHolder' => 1])
    </div>
    <div class="panel-post-datetime-container">
        <div class="panel-post-offer-type">
            <span>{{ trans('offer.offersType') }}</span>
            <div class="btn-group" role="group">
                @foreach($offersTypeChoice as $typeO=>$parametersO)
                    <label class="btn  @if($post->offer_type == $typeO) active @endif">
                        {{ Form::radio('offer_type', $typeO, ($post->offer_type == $typeO) ? true : false ) }}
                        {{ trans('offer.choice.'.$typeO) }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form-group panel-post-datetime">
            <div class="panel-post-datetime-wrapper">
                <div>
                    <label for="panel-event-start-date">{{ trans('offer.start_at') }}</label>
                    <span class="input-ctn">
                        {{ Form::date('date', $post->start_at, ['class' => 'form-control panel-post-date-input '.(($errors->has('start_at')) ? 'is-invalid' : ''), 'id' => 'panel-event-start-date']) }}
                    </span>
                </div>
            </div>

            <div class="panel-post-datetime-wrapper">
                <div>
                    <label for="panel-event-end-date">
                        {{ trans('offer.stop_at') }}
                        <small>({{ trans('offer.stop_at_optional') }})</small>
                    </label>
                    <span class="input-ctn">
                        {{ Form::date('date_end', $post->stop_at, ['class' => 'form-control panel-post-date-input '.(($errors->has('stop_at')) ? 'is-invalid' : ''), 'id' => 'panel-event-end-date']) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    {{ Form::label('content', trans('offer.description'), ['class' => 'sr-only'] ) }}
    {{ Form::textarea('content', $post->content, ['rows' => 3, 'class' => 'form-control mentions autogrow panel-textarea '.(($errors->has('content')) ? 'is-invalid' : ''), 'id' => 'form-offer-content', 'placeholder' => trans('offer.description')]) }}

    @if ($errors->has('content'))
        <span class="invalid-feedback">{!! $errors->first('content') !!}</span>
    @endif
</div>

<div class="imported-link">
@if(isset($linksIds) && !empty($linksIds))
    @foreach(explode(',',$linksIds) AS $linkId)
        @include('posting.content-types.link-preview', ['id' => $linkId])
    @endforeach
@endif
</div>

@if($modal)
<script type="text/javascript">
(function () {
    var miniMap = $('#modal-ajax');
    new MiniMapForm({
        $wrapper: miniMap,
        $latitude: {{ ($post->latitude != '') ? $post->latitude : session("lat") }},
        $longitude: {{ ($post->longitude != '') ? $post->longitude : session("lng") }},
        $displayMap: {{ ($post->id != null || ($post->latitude && $post->longitude) ) ? 'true' : 'false' }},
        $placeName: '',
        $elementType: '{{ class_basename($post) }}'
    });
})();
</script>
@endif