<div class="modal-header">
    <h4 class="modal-title">{{ trans('playlist.create_a_playlist') }}</h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">

        {{ Form::open(array('route' => 'playlist_create', 'id' => 'playlistCreateForm', 'name' => 'playlistCreateForm')) }}
            <div class="form-group clearfix @if ($errors->has('author_id') | $errors->has('author_type')) has-error @endif">
                <div class="float-left">
                    <h5>
                        {{ trans('netframe.publishAs') }} : {!! HTML::publishAs('#playlistCreateForm', $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'pl'], true, (isset($inputs['author_id'])) ? ['id'=>$inputs['author_id'], 'type'=>$inputs['author_type']] : null
                        ) !!}
                    </h5>

                    @if ($errors->has('author_id') || $errors->has('author_type'))
                        <span class="help-block">{{ trans('playlist.publish_as_not_selected_error') }}</span>
                    @endif
                </div>
            </div>

            <div id="publish-as-hidden-pl">
                {{ Form::hidden("author_id", (isset($inputs['author_id']) ? $inputs['author_id'] : auth()->guard('web')->user()->id) ) }}
                {{ Form::hidden("author_type", (isset($inputs['author_type']) ? $inputs['author_type'] : 'user') ) }}
                {{-- Form::hidden('redirect_playlist_id', $playlist->id ) --}}
            </div>

            <div class="form-group @if ($errors->has('name')) has-error @endif">
                {{ Form::label('name', trans('playlist.name') ) }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}

                @if ($errors->has('name'))
                    <span class="help-block">{{ $errors->first('name') }}</span>
                @endif
            </div>

            <div class="form-group @if ($errors->has('description')) has-error @endif">
                {{ Form::label('description', trans('playlist.description') ) }}
                {{ Form::textarea('description', null, array('class' => 'form-control')) }}

                @if ($errors->has('description'))
                    <span class="help-block">{{ $errors->first('description') }}</span>
                @endif
            </div>
        </div>
        {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('form.close') }}</button>
    <button type="submit" class="btn btn-primary">
        {{ trans('form.publish') }}
    </button>
</div>
{{ Form::close() }}
<!-- End MODAL-FOOTER -->
