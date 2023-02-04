<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('messages.new'.ucfirst($types[$type+$overrideType])) }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body nf-form">
    @include('messages.form-content')

    <div class="nf-form-validation">
        <button type="submit" class="nf-btn btn-primary btn-xxl">
            <div class="btn-txt">
                {{ trans('form.send') }}
            </div>
            <div class="svgicon btn-img">
                @include('macros.svg-icons.arrow-right')
            </div>
        </button>
    </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->