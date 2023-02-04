<div class="display-edit">
    {{ Form::open(array('route' => 'media_upload', 'files' => true, 'id' => 'updateMedia', 'name' => 'upload')) }}
        <span class="fileinput-button">
            <span class="svgicon white-bg">
                @include('macros.svg-icons.attach')
            </span>
            {{ Form::file('files[]', array('accept' => '*')) }}
        </span>
        <div class="row d-none" id="progress-files">
            <div class="col-md-12">
                <div class="progress progress-upload active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                    <div class="progress-bar progress-bar-upload" style="width:0%;"></div>
                </div>
            </div>
        </div>
    {{ Form::close() }}
</div>

<script>
(function () {
     var attachmentSystem = $('#in-edit-file');
     new AttachmentSystem({
         $wrapper: attachmentSystem,
         $previewDomClass: '.in-edit-file',
         $fileUpload: attachmentSystem.find('#updateMedia'),
         $profileMedia: 1,
         $postMedia: 0,
         $confidentiality: 0,
         $profileId: {{ $media->author()->first()->id }},
         $profileType: '{{ $media->author()->first()->getType() }}',
         $mediaId: {{ $media->id }}
     });
 })();
</script>