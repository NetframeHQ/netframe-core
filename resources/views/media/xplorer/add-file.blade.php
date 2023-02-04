<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('xplorer.file.add.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <div>
        @if(!session('reachInstanceQuota') && !session('reachUserQuota'))
            {{ Form::open(array('route' => 'media_upload', 'files' => true, 'id' => 'fileupload', 'name' => 'upload')) }}
                <span class="btn btn-border-default btn-sm fileinput-button in-xplorer">
                    <span>
                        {{ trans('xplorer.file.add.modalButton') }}<br>
                        ({{ trans('media::messages.add_from_cam_photo') }} / {{ trans('media::messages.add_from_cam_video') }} / {{ trans('media::messages.add_document') }})
                    </span>
                    {{ Form::file('files[]', array('multiple' => true, 'accept' => '*')) }}
                </span>
            {{ Form::close() }}

            @if(isset($activeWorkflow) && $activeWorkflow && isset($forceWorkflow) && $forceWorkflow)
                <div class="mb-3">
                    <div class="input-group search-file-wrapper">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">{{ trans('xplorer.file.add.or') }}</span>
                        </div>
                        {{ Form::text('search-file', '', ['class' => 'form-control', 'id' => 'search-file-input', 'placeholder' => trans('xplorer.file.add.searchFile')]) }}
                    </div>
                </div>
            @endif
        @else
            <a href="{{ url()->route('media.quota.reach') }}" class="btn btn-border-default btn-sm fileinput-button" data-target="#modal-ajax" data-toggle="modal">
                <span>
                    {{ trans('xplorer.file.add.modalButton') }}<br>
                    ({{ trans('media::messages.add_from_cam_photo') }} / {{ trans('media::messages.add_from_cam_video') }} / {{ trans('media::messages.add_document') }})
                </span>
            </a>
        @endif

        {{ Form::open(['route' => ['xplorer_add_file', 'profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder ], 'class' => 'no-auto-submit fn-add-file nf-form']) }}
            {{ Form::hidden('taskId', $attachToExistingTask) }}
            <ul class="tl-posted-medias block-mosaic row mg-0">
                @if(isset($inputOld))
                    @foreach($inputOld['file-id'] as $fileId)
                        <li class="clearfix">
                            <ul class="list-inline">
                            <li class="list-inline-item template-download fade mosaic-item col-md-2 col-xs-3 file-{{ $fileId }} in" data-file-id="{{ $fileId }}">
                                <img src="{{ url()->route('media_download', ['id' => $fileId, 'thumb' => 1] ) }}" class="img-fluid img-thumbnail" />
                            </li>
                            <input type="hidden" name="file-id[]" value="{{ $fileId }}">
                            <li class="list-inline-item col-md-10 col-xs-9 file-{{ $fileId }}" data-file-id="{{ $fileId }}">
                                <a class="fn-remove-media float-right">
                                    X
                                </a>
                                <div class="form-group @if ($errors->has('filename-'.$fileId)) has-error @endif">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ trans('xplorer.file.add.filename') }}
                                            </div>
                                        </div>
                                        <input type="text" name="filename-{{ $fileId }}" value="{{ $inputOld['filename-'.$fileId] }}" class="form-control">
                                        @if(isset($driveFolder))
                                        <input type="hidden" name="driveFolder" value="{{$driveFolder}}"/>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ trans('xplorer.file.add.tags') }}
                                            </div>
                                        </div>
                                        {{ Form::select('tags-'.$fileId.'[]',
                                            (isset($tags[$fileId]['formTags'])) ? $tags[$fileId]['formTags'] : [],
                                            (isset($tags[$fileId]['formTagsSelecteds'])) ? $tags[$fileId]['formTagsSelecteds'] : null,
                                            ['id' => 'tags', 'class' => 'form-control fn-select2-tag', 'multiple' => 'multiple']) }}
                                    </div>
                                </div>
                            </li>
                            </ul>
                            </li>
                    @endforeach
                @endif
            </ul>

            <div class="d-none">
                {{ Form::hidden('confidentiality', $confidentiality) }}
                {{--
                    <input type="radio" name="confidentiality" value="0" autocomplete="off">
                    <input type="radio" name="confidentiality" value="1" autocomplete="off" checked="checked">
                --}}
            </div>

           <div class="nf-form-validation">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('xplorer.file.add.submit') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
           @if(isset($activeWorkflow) && $activeWorkflow)
                @include('workflow.xplorer.modal', ['profile_id' => $profileId, 'profile_type' => $profileType])
            @endif
            @if(isset($driveFolder))
                {{ Form::hidden('parent', $driveFolder) }}
            @endif

        {{ Form::close() }}
    </div>

</div>
<!-- End MODAL-BODY -->

<script>
    (function () {
         var attachmentSystem = $('#modal-files');
         new AttachmentSystem({
             $wrapper: attachmentSystem,
             $fileUpload: attachmentSystem.find('#fileupload'),
             $profileId: {{ $profileId }},
             $profileType: '{{ $profileType }}',
             $fromXplorer: 1,
             $idFolder: {{ $idFolder }},
             $mediaTemplateRender: '.tl-posted-medias',
             $confidentiality: attachmentSystem.find('input:radio[name=confidentiality]'),
             $checkExist: true
         });

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

    const SearchFileBar = new autoComplete({
        data: {
          src: async () => {
            var query = document.querySelector("#search-file-input").value;
            var headers = new Headers();
            headers.append("Accept", "application/json");
            var init = { method: 'GET', headers: headers };
            var source = await fetch(`/search?term=${query}&types[0]=medias`, init);
            var data = await source.json();
            return data;
          },
          key: ["label"],
          cache: false
        },
        selector: "#search-file-input",
        observer: true,
        threshold: 1,
        debounce: 300,
        searchEngine: (query, record) => { return record; },
        resultsList: {
            destination: ".search-file-wrapper",
            position: "afterend",
            element: "ul",
            className: "nf-autocomplete"
        },
        maxResults: 5,
        resultItem: {
            content: (data, source) => {
                source.innerHTML = `
                  ${data.value.thumb}
                  <span class="text">${data.match}</span>
                `;
            },
            element: "li",
            highlight: true
        },
        noResults: (dataFeedback, generateList) => {
            generateList(autoCompleteJS, dataFeedback, dataFeedback.results);
            const result = document.createElement("li");
            result.setAttribute("class", "no_result");
            result.setAttribute("tabindex", "1");
            result.innerHTML = `<span>Found No Results for "${dataFeedback.query}"</span>`;
            document.querySelector(`#${autoCompleteJS.resultsList.idName}`).appendChild(result);
        },
        onSelection: feedback => {
            let mediaId = feedback.selection.value.id.split('-');
            mediaId = mediaId[1];
            //@TODO call back end to gett all medias properties and launch form to display media in modal
            //window.document.location = feedback.selection.value.value;
            $.ajax({
                url: laroute.route('media_download', {id: mediaId}) + '?returnJson=1',
                type: "GET",
                success: function (data) {
                    console.log(data);
                    var container = $('#template-media-select-tasks').html()
                    var templateContainer = Handlebars.compile(container);
                    var contextContainer = {
                            files: data.files
                    };
                    var htmlContainer = templateContainer(contextContainer);

                    $(".tl-posted-medias").append(htmlContainer);
                }
            });

        },
        events: {
            input: {
                selection: (event) => {
                    const selection = event.detail.selection.value;
                    autoCompleteJS.input.value = selection;
                }
            }
        }
    });
</script>
