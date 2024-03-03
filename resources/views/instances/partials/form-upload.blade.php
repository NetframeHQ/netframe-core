<div class="display-edit">
    {{ Form::open(['route' => 'instances.upload', 'files' => true, 'class' => 'fileupload', 'id'=> $image]) }}
        <span class="fileinput-button">
            {{ Form::file('file', array('accept' => 'image/jpeg, image/pjpeg, image/png, image/gif')) }}
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

@section('javascripts')
@parent
<script>
(function () {
     let instanceMedia = $('div#{{ $image }}');
     new InstancesMedias({
         $wrapper: instanceMedia,
         $previewDomClass: '.{{ $image }}',
         $fileUpload: instanceMedia,
         $mediaType: '{{ $image }}',
         $finalField: '{{ $finalField }}',
         $inBackground: {{ (isset($inBackground) && $inBackground == 1) ? 1 : 0 }}
     });
 })();
</script>
@stop