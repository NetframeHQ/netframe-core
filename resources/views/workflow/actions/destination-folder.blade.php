<div class="wf_action_container" data-field="actionDatas_{{ $actionSlug }}">
    <span>{{ trans('netframe.publishOn') }} :</span>
    {!! HTML::publishAs('.fn-create-wf-move-file',
        $NetframeProfiles, [
            'id'=>'id_foreign',
            'type'=>'type_foreign',
            'postfix'=>'mf'
        ],
        true, [
        'id' => $profileId,
        'type' => $profileType]
    ) !!}
    <div id="publish-as-hidden-mf">
        {{ Form::hidden("id_foreign", $profileId) }}
        {{ Form::hidden("type_foreign", $profileType) }}
    </div>

    <div class="form-group">
        {{ Form::label('actionDatas_'.$actionSlug, trans('workflow.actionsHelpers.destinationFolder')) }}
        {{ Form::select('actionDatas_'.$actionSlug, $folders, null, ['class' => 'form-control']) }}
    </div>
</div>