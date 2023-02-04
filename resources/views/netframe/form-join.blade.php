<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('netframe.join')  }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body nf-form">

    {{ Form::open(['route'=> 'join.ask.post', 'id' => 'form-join-ask', 'files' => true]) }}

    {{ Form::hidden("users_id", (isset($inputOld['users_id']) ? $inputOld['users_id'] : $users_id ) ) }}
    {{ Form::hidden("profile_id", (isset($inputOld['profile_id']) ? $inputOld['profile_id'] : $profile_id) ) }}
    {{ Form::hidden("profile_type", (isset($inputOld['profile_type']) ? $inputOld['profile_type'] : $profile_type) ) }}

    <label class="nf-form-cell nf-cell-full @if($errors->has('content')) nf-cell-error @endif">
        {{ Form::textarea(
            'content',
            ((isset($inputOld['content'])) ? $inputOld['content'] : trans('members.join.defaultMessage').' '.trans('members.join.'.$profile_type)),
            ['rows'=>'3', 'class'=>'nf-form-input mentions', 'id' => 'form-share-content']
        ) }}
        <span class="nf-form-label">
            {{ trans('form.message') }}
        </span>
        {!! $errors->first('content', '<p class="invalid-feedback">:message</p>') !!}
        <div class="nf-form-cell-fx"></div>
    </label>

    <div class="nf-form-validation">
        <button type="submit" class="nf-btn btn-primary btn-xxl">
            <div class="btn-txt">
                {{ trans('netframe.join') }}
            </div>
            <div class="svgicon btn-img">
                @include('macros.svg-icons.arrow-right')
            </div>
        </button>
    </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->
