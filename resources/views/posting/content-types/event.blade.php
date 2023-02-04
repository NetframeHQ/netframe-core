<div class="panel-post-input-head">
    <div class="form-group panel-post-name">
        {{ Form::label('title', trans('event.event_title'), ['class' => 'sr-only']) }}
        {{ Form::text('title', $post->title, ['class' => 'form-control '.(($errors->has('title')) ? 'is-invalid' : ''), 'placeholder' => trans('event.event_title'), 'autocomplete' => 'off']) }}

        @if ($errors->has('title'))
            <span class="invalid-feedback">{!! $errors->first('title') !!}</span>
        @endif
    </div>
    <div class="form-group panel-post-place">
        @include('location.minimap-form', ['profile' => $post, 'mapName' => 'mini-map-form-ajax', 'noSearchHolder' => 1 ])
    </div>
    <div class="panel-post-datetime-container">
        <div class="form-group panel-post-datetime">
            <div class="panel-post-datetime-wrapper">
                <div>
                    <label for="panel-event-start-date">{{ trans('event.event_date') }}</label>
                    <span class="input-ctn">
                        {{ Form::date('date', $post->date, ['class' => 'form-control panel-post-date-input '.(($errors->has('date')) ? 'is-invalid' : ''), 'id' => 'panel-event-start-date']) }}
                    </span>
                </div>
                <div class="time-selector">
                    <label for="panel-event-start-time">{{ trans('event.on') }}</label>
                    <span class="input-ctn">
                        {{ Form::time('time', $post->time, ['class' => 'form-control panel-post-time-input'.(($errors->has('time')) ? 'is-invalid' : ''), 'id' => 'panel-event-start-time']) }}
                    </span>
                </div>
                @if ($errors->has('date'))
                    <span class="invalid-feedback d-block">{!! $errors->first('date') !!}</span>
                @endif
                @if ($errors->has('time'))
                    <span class="invalid-feedback d-block">{!! $errors->first('time') !!}</span>
                @endif
            </div>

            <div class="panel-post-datetime-wrapper">
                <div>
                    <label for="panel-event-end-date">{{ trans('event.event_date_end') }}</label>
                    <span class="input-ctn">
                        {{ Form::date('date_end', $post->date_end, ['class' => 'form-control panel-post-date-input '.(($errors->has('date_end')) ? 'is-invalid' : ''), 'id' => 'panel-event-end-date']) }}
                    </span>
                </div>
                <div class="time-selector">
                    <label for="panel-event-end-time">{{ trans('event.on') }}</label>
                    <span class="input-ctn">
                        {{ Form::time('time_end', $post->time_end, ['class' => 'form-control panel-post-time-input'.(($errors->has('time_end')) ? 'is-invalid' : ''), 'id' => 'panel-event-end-time']) }}
                    </span>
                </div>
                @if ($errors->has('date_end'))
                    <span class="invalid-feedback d-block">{!! $errors->first('date_end') !!}</span>
                @endif
                @if ($errors->has('time_end'))
                    <span class="invalid-feedback d-block">{!! $errors->first('time_end') !!}</span>
                @endif
            </div>

            <div class="form-element panel-event-allday-ctn">
                <label for="panel-event-allday">
                    <div class="nf-checkbox">
                        {{ Form::checkbox('all_day', 1, ($post->all_day == 1), ['id' => 'panel-event-allday', 'class' => '']) }}
                    </div>
                    <span class="text">{{ trans('event.event_all_day') }}</span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    {{ Form::label('description', trans('event.description'), ['class' => 'sr-only']) }}
    {{ Form::textarea('description',
        $post->description,
        ['rows' => 3, 'class' => 'form-control mentions autogrow panel-textarea '.(($errors->has('description')) ? 'is-invalid' : ''), 'id' => 'form-event-content', 'placeholder' => trans('event.description')]
    ) }}

    @if ($errors->has('description'))
        <span class="invalid-feedback">{!! $errors->first('description') !!}</span>
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

<script>
    (function () {
         var miniMap = $('#modal-ajax');
         new MiniMapForm({
             $wrapper: miniMap,
             $latitude: {{ ($post->latitude != '') ? $post->latitude : session("lat") }},
             $longitude: {{ ($post->longitude != '') ? $post->longitude : session("lng") }},
             $displayMap: {{ ($post->id != null || ($post->latitude && $post->longitude) ) ? 'true' : 'false' }},
             $placeName: '',
             $elementType: '{{ get_class($post) }}'
         });
     })();
     </script>

@endif