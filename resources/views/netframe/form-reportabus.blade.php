<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('netframe.reportAbuseTitle') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

@if(isset($messageSuccess))
<div class="modal-body">
    <div class="alert alert-success text-center">
        <p>{{ trans('netframe.reportAbuseSuccess') }}</p>
    </div>
</div>
@elseif(isset($messageInfo))
<div class="modal-body">
    <div class="alert alert-info text-center">
        <p>{{ trans('netframe.reportAbuseInfo') }}</p>
    </div>
</div>
@else
{{ Form::open() }}
<div class="modal-body">

    {{ Form::hidden("users_id_property", $authorId) }}
    {{ Form::hidden("post_id", $postId) }}
    {{ Form::hidden("post_type", $postType) }}
    {{ Form::hidden("newsfeed", $newsFeedsId) }}

    <p>
        <em>{{ trans('netframe.reportAbuseDescription') }}</em>
    </p>
    <hr />

    @if(isset($messages))
        @foreach($messages->all() as $message)
        <div class="alert alert-danger">
            <ul class="list-unstyled">
            	<li>{{ $message }}</li>
            </ul>
        </div>
        @endforeach
    @endif

    <h2><u>{{ trans('netframe.reportAbuseWhatType') }}</u></h2>
    <div class="well well-sm">
        @foreach(config('netframe.typeAbuse') as $item)
        <div class="form-group">
            <label>
                <input type="radio" name="type_abuse" value="{{ $item }}" autocomplete="off">
                {{ trans('form.reportAbuse.'.$item) }}
            </label>
        </div>
        @endforeach
    </div>
</div>
<!-- End MODAL-BODY -->
<div class="modal-footer">
    <button type="submit" class="button primary">
        {{ trans('form.send') }}
    </button>
</div>
{{ Form::close() }}
@endif
<!-- End MODAL-FOOTER -->
