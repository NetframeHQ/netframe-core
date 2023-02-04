<div class="row wf-line">
    <div class="col-10 col-md-5">
        <div class="form-group">
            {{ Form::hidden('actionSlug[]', $fieldSlug, ['class' => 'action-slug']) }}
            {{ Form::select('actionType_'.$fieldSlug, [0 => trans('workflow.actions.selectValue')] + $actions, '', ['class' => 'form-control select-action']) }}
        </div>
    </div>
    <div class="col-10 col-md-5 action-details d-none">
    </div>
    <div class="col-2 col-md-1">
        <a class="wf-remove-line">
            <span class="svgicon">
                @include('macros.svg-icons.trash')
            </span>
        </a>
    </div>
</div>