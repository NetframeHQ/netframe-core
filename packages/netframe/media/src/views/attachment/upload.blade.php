{{ Form::open(array('route' => 'media_upload', 'files' => true, 'id' => 'fileupload', 'name' => 'upload')) }}

    <div class="fileupload-buttonbar">
            {{-- si on passe par le formulaire de création de talent --}}
            @if(isset($fromFormTalent) && $fromFormTalent === true)
                @if($acceptFormat == '*')
                    <span class="btn btn-default fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>{{ trans('media::messages.add_from_cam_photo') }}</span>
                        {{ Form::file('files[]', array('multiple' => true, 'capture' => 'camera', 'accept' => 'image/*' )) }}
                    </span>
                    <span class="btn btn-default fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>{{ trans('media::messages.add_from_cam_video') }}</span>
                        {{ Form::file('files[]', array('multiple' => true, 'capture' => 'camcorder', 'accept' => 'video/*')) }}
                    </span>
                    <span class="btn btn-default fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>{{ trans('media::messages.add_documents') }}</span>
                        {{ Form::file('files[]', array('multiple' => true,  'accept' => '*')) }}
                    </span>
                    <span class="btn btn-default fileimport-button" id="displayImport">
                        <i class="glyphicon glyphicon-cloud-download"></i>
                        <span>
                            {{ trans('media::messages.import_from') }}
                            @foreach($importers as $importer)
                                <span class="{{ $importer->getDescription()['icon'] }}"></span>
                            @endforeach
                        </span>
                    </span>
                @else
                    <span class="btn btn-default fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>{{ trans('media::messages.add_from_cam_photo') }}</span>
                            {{ Form::file('files[]', array('capture' => 'camera', 'accept' => $acceptFormat)) }}
                    </span>
                    <span class="btn btn-default fileinput-button">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>{{ trans('media::messages.choose_photo') }}</span>
                            {{ Form::file('files[]', array('accept' => $acceptFormat)) }}
                    </span>
                @endif
            {{-- si on change la photo d'un profile --}}
            @elseif(isset($profilePicture) && $profilePicture === true)
                <span class="btn btn-default fileinput-button visible-xs">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>{{ trans('media::messages.add_from_cam_photo') }}</span>
                        {{ Form::file('files[]', array('capture' => 'camera', 'accept' => 'image/*')) }}
                </span>
                <span class="btn btn-default fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>{{ trans('media::messages.choose_photo') }}</span>
                        {{ Form::file('files[]', array('accept' => 'image/*')) }}
                </span>
            {{-- si on ajoute un média via le menu ou un post --}}
            @else
                <span class="btn btn-default fileinput-button mg-bottom">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>{{ trans('media::messages.add_from_cam_photo') }}</span>
                    {{ Form::file('files[]', array('multiple' => true, 'capture' => 'camera', 'accept' => 'image/*' )) }}
                </span>
                <span class="btn btn-default fileinput-button mg-bottom">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>{{ trans('media::messages.add_from_cam_video') }}</span>
                    {{ Form::file('files[]', array('multiple' => true, 'capture' => 'camcorder', 'accept' => 'video/*')) }}
                </span>
                <span class="btn btn-default fileinput-button mg-bottom">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>{{ trans('media::messages.add_documents') }}</span>
                    {{ Form::file('files[]', array('multiple' => true,  'accept' => '*')) }}
                </span>
                <span class="btn btn-default fileimport-button mg-bottom" id="displayImport">
                    <i class="glyphicon glyphicon-cloud-download"></i>
                    <span>
                        {{ trans('media::messages.import_from') }}
                        @foreach($importers as $importer)
                            <span class="{{ $importer->getDescription()['icon'] }}"></span>
                        @endforeach
                    </span>
                </span>
            @endif
    </div>
    <div class="row hidden" id="progress-files">
        <div class="col-md-12">
            <div class="progress progress-upload active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar-upload" style="width:0%;"></div>
            </div>
        </div>
        <div class="col-md-12 progress-time text-right datetime-sm">{{ trans('media::messages.reaminingTime') }} <span class="remaining-time"></span></div>
    </div>
{{ Form::close() }}

{{--
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            	<div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-success start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>{{ trans('media::messages.start') }}</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-default cancel">
                    <i class="glyphicon glyphicon-remove"></i>
                    <span>{{ trans('media::messages.cancel') }}</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>


<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">{{ trans('media::messages.error') }}</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td></td>
    </tr>
{% } %}
</script>
--}}
