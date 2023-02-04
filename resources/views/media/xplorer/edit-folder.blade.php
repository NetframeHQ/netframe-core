<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('xplorer.folder.add.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body nf-form">

    {{ Form::open(['route'=> ['xplorer_edit_folder', 'profileType' => $profileType, 'profileId' => $profileId], 'class' => 'no-auto-submit fn-add-folder']) }}
        @if(isset($folder) && $folder != null)
            {{ Form::hidden('id', $folder->id) }}
        @endif

        @if(isset($driveFolder) && $driveFolder != null)
            {{ Form::hidden('parent', $driveFolder) }}
        @endif

        <!-- NAME -->
        <label class="nf-form-cell nf-cell-full @if($errors->has('name')) nf-cell-error @endif">
            {{ Form::text('name', (isset($folder) && $folder != null) ? $folder->name : '', ['class' => 'nf-form-input'] ) }}
            <span class="nf-form-label">
                {{ trans('xplorer.folder.add.folderName') }}
            </span>
            {!! $errors->first('name', '<p class="invalid-feedback">:message</p>') !!}
            <div class="nf-form-cell-fx"></div>
        </label>

        @if((array_key_exists('parentFolder', compact('parentFolder')) && $parentFolder == null))
            <label class="nf-form-cell nf-form-checkbox" for="publicFolder">
                {{ Form::checkbox(
                    'publicFolder',
                    '1',
                    (isset($folder) && $folder != null) ? $folder->public_folder : false,
                    ['class' => 'nf-form-input', 'id' => 'publicFolder']
                ) }}
                <span class="nf-form-label">
                    {{ trans('xplorer.folder.add.publicFolder') }}
                </span>
                <div class="nf-form-cell-fx"></div>
            </label>
        @endif

       <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ ((isset($folder) && $folder != null) ? trans('xplorer.folder.add.update') : trans('xplorer.folder.add.create')) }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->


