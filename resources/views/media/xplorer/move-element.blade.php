<div class="modal-header">
    <h4 class="modal-title">
        <span class="glyphicon glyphicon glyphicon-folder-open"></span> {{ trans('xplorer.'.$typeAction.'Element.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body nf-form">
    {{ Form::open(['route' => 'xplorer_'.$typeAction.'_element', 'class' => 'no-auto-submit fn-move-file', 'id' => 'form-move-element']) }}
        <!-- NAME -->
        <label class="nf-form-cell nf-cell-full">
            {!! HTML::publishAs(
                '.fn-move-file',
                $NetframeProfiles, [
                    'id'=>'id_foreign',
                    'type'=>'type_foreign',
                    'postfix'=>'mf'
                ],
                true, [
                'id' => $profileId,
                'type' => $profileType]
            ) !!}
            <span class="nf-form-label">
                {{ trans('netframe.publishOn') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>

        <label class="nf-form-cell nf-cell-full">
            {{ Form::select('target', $folders, null, ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('xplorer.'.$typeAction.'Element.chooseTarget') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>

        <div id="publish-as-hidden-mf">
            {{ Form::hidden("id_foreign", $profileId) }}
            {{ Form::hidden("type_foreign", $profileType) }}
        </div>
        {{ Form::hidden('movedElementType', $movedElementType) }}
        {{ Form::hidden('movedElementId', $movedElementId) }}

        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('xplorer.'.$typeAction.'Element.submit') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->
