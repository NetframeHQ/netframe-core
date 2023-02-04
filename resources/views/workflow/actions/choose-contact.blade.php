{{ Form::select('actionDatas_' . $actionSlug, [], '', ['class' => 'form-control '.$fieldClass]) }}
<div class="form-inline">
    {{ Form::label('date' . $fieldClass, trans('workflow.targetDate')) }}
    {{ Form::date('actionDate_' . $actionSlug, '', ['class' => 'form-control ']) }}
</div>