<div class="modal-header">
    <h4 class="modal-title">
        <span class="glyphicon glyphicon glyphicon-file"></span> {{ trans('xplorer.file.edit.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <div>
            <ul class="tl-posted-medias block-mosaic row mg-0">
                <li class="clearfix">
                    <ul class="list-inline">
                        <li class="template-download list-inline-item mosaic-item col-12 col-md-2  in">
                            <div class="hover-edit mg-bottom" id="in-edit-file">
                                {!! \HTML::thumbnail($media, 200, 200, array('class' => 'img-fluid in-edit-file')) !!}
                                @if($rights && $rights <= 4)
                                    @include('media.xplorer.form-update-file')
                                @endif

                                <span class="drag-text">
                                    {{ trans('xplorer.file.updateFile') }}
                                </span>
                            </div>
                        </li>
                        <li class="list-inline-item col-xs-12 col-md-10 nf-form nf-col-2">
                            {{ Form::open(['route' => ['xplorer_edit_file' ], 'class' => 'no-auto-submit fn-add-file']) }}
                                {{ Form::hidden('idFile', $media->id) }}
                                    <label class="nf-form-cell @if($errors->has('name')) nf-cell-error @endif">
                                        <input type="text" name="filename" value="{{ $media->name }}" class="nf-form-input">
                                        <span class="nf-form-label">
                                            {{ trans('xplorer.file.add.filename') }}
                                        </span>
                                        {!! $errors->first('name', '<div class="nf-form-feedback">:message</div>') !!}
                                        <div class="nf-form-cell-fx"></div>
                                    </label>
                                    <label class="nf-form-cell @if($errors->has('author')) nf-cell-error @endif">
                                        <input type="text" name="author" value="{{ $media->meta_author }}" class="nf-form-input">
                                        <span class="nf-form-label">
                                            {{ trans('xplorer.file.add.author') }}
                                        </span>
                                        {!! $errors->first('author', '<div class="nf-form-feedback">:message</div>') !!}
                                        <div class="nf-form-cell-fx"></div>
                                    </label>
                                    <label class="nf-form-cell">
                                        <textarea name="description" class="nf-form-input">{{ $media->description }}</textarea>
                                        <span class="nf-form-label">
                                            {{ trans('xplorer.file.add.description') }}
                                        </span>
                                        <div class="nf-form-cell-fx"></div>
                                    </label>
                                    <label class="nf-form-cell tags-add">
                                        {{ Form::select(
                                            'tags[]',
                                            (isset($tags['formTags'])) ? $tags['formTags'] : $media->tagsList(),
                                            (isset($tags['formTagsSelecteds'])) ? $tags['formTagsSelecteds'] : $media->tagsList(true),
                                            ['id' => 'tags', 'class' => 'nf-form-input fn-select2-tag', 'multiple' => 'multiple']
                                        ) }}
                                        <span class="nf-form-label">
                                            {{ trans('xplorer.file.add.tags') }}
                                        </span>
                                        <div class="nf-form-cell-fx"></div>
                                    </label>
                                    <div class="d-none">
                                        <input type="radio" name="confidentiality" value="0" autocomplete="off">
                                        <input type="radio" name="confidentiality" value="1" autocomplete="off" checked="checked">
                                    </div>
                                   <div class="nf-form-validation">
                                        <button type="submit" class="nf-btn btn-primary btn-xxl">
                                            <div class="btn-txt">
                                                {{ trans('xplorer.file.edit.submit') }}
                                            </div>
                                            <div class="svgicon btn-img">
                                                @include('macros.svg-icons.arrow-right')
                                            </div>
                                        </button>
                                    </div>
                                {{ Form::close() }}
                        </li>
                    </ul>
                </li>
            </ul>
    </div>



</div>
<!-- End MODAL-BODY -->

<script>
    (function () {
        /*
         var attachmentSystem = $('#modal-files');
         new AttachmentSystem({
             $wrapper: attachmentSystem,
             $fileUpload: attachmentSystem.find('#fileupload'),
             $profileId: {{-- $profileId --}},
             $profileType: '{{--- $profileType --}}',
             $fromXplorer: 1,
             $idFolder: {{-- $idFolder --}},
             $mediaTemplateRender: '.tl-posted-medias',
             $confidentiality: attachmentSystem.find('input:radio[name=confidentiality]')
         });
         */

         $('.fn-select2-tag').select2({
             language: "{{ Lang::locale() }}",
             minimumInputLength: 2,
             multiple: true,
             ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                 url: "{{ URL::route('tags.autocomplete') }}",
                 dataType: 'json',
                 contentType: "application/json",
                 type: "POST",
                 data: function (params) {
                     return  JSON.stringify({
                         q: params.term
                     });
                 },
                 processResults: function (data, page) {
                     return data;
                 },
             },
             escapeMarkup: function (markup) { return markup; },
         });
     })();
</script>
