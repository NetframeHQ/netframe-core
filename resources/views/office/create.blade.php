<div class="modal-header">
    <h4 class="modal-title">
        {{trans('office.create').' '.lcfirst(trans('office.'.$documentType))}}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body nf-form">

    {{ Form::open() }}
    <!-- NAME -->
    <label class="nf-form-cell nf-cell-full">
        {{ Form::text('name', '', ['class'=>'nf-form-input']) }}
        <span class="nf-form-label">
            {{ trans('office.name') }}
        </span>
        <div class="nf-form-cell-fx"></div>
    </label>

    <div class="nf-form-validation">
        <button type="submit" class="nf-btn btn-primary btn-xxl">
            <div class="btn-txt">
                {{ trans('form.save') }}
            </div>
            <div class="svgicon btn-img">
                @include('macros.svg-icons.arrow-right')
            </div>
        </button>
    </div>
    {{ Form::close() }}
</div>
