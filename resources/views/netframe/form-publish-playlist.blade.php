<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('form.publishPlaylist') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">

    {{ Form::open(['url'=> '/netframe/publish-playlist', 'id' => 'form-publish-playlist']) }}

    <div class="form-group clearfix">

        <div class="col-md-12">
            <h5>
                {{ trans('netframe.publishAs') }} :
                {{ HTML::publishAs('#form-publish-playlist', $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'pp'], false, (isset($inputOld['author_id'])) ? ['id'=>$inputOld['author_id'], 'type'=>$inputOld['author_type']] : null) }}
            </h5>
        </div>
    </div>

    <div id="publish-as-hidden-pp">
        {{ Form::hidden("author_id", (isset($inputOld['author_id'])) ? $inputOld['author_id'] : auth()->guard('web')->user()->id) }}
        {{ Form::hidden("author_type", (isset($inputOld['author_type'])) ? $inputOld['author_type'] : 'user') }}
    </div>

    {{ Form::hidden("id_playlist", $playlist->id) }}
    {{ Form::hidden("edit", (isset($inputOld['edit']) ? $inputOld['edit'] : $edit)) }}

    {{ Form::message() }}
    <div class="form-group @if ($errors->has('content')) has-error @endif">
        {{ Form::label('content', trans('form.message')) }}
        <br />
        {{ Form::textarea( 'content', (isset($inputOld['content']) ? $inputOld['content'] : ''), ['rows'=>'7', 'class'=>'form-control'] ) }}
        {{ $errors->first('content', '<p class="help-block">:message</p>') }}
    </div>

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
