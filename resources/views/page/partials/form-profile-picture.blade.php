<div class="btn-inputupload">
    {{ Form::open(array('route' => 'media_upload', 'files' => true, 'id' => 'fileupload', 'name' => 'upload')) }}
        <span class="fileinput-button">
            @if(isset($customButton) && !empty($customButton))
                {!! $customButton !!}
            @else
                <span class="svgicon">
                    @include('macros.svg-icons.attach')
                </span>
            @endif
            {{ Form::file('files[]', array('accept' => 'image/*')) }}
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

@if(!isset($disable_extra_links) || $disable_extra_links == 0)
    <ul class="nf-actions">
        <li class="nf-action">
            <a class="nf-btn btn-ico">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.edit')
                </span>
            </a>
        </li>
        <li class="nf-action">
            <a class="nf-btn btn-ico">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.x')
                </span>
            </a>
        </li>
    </ul>
@endif

@section('javascripts')
@parent
<script>
(function () {
     var attachmentSystem = $('#profile-edit-form');
     new AttachmentSystem({
         $wrapper: attachmentSystem,
         $previewDomClass: '.nf-cell-avatar #js-profile-picture #profile-image-container',
         $fileUpload: attachmentSystem.find('#fileupload'),
         $profileMedia: 1,
         $postMedia: 0,
         $confidentiality: 0,
         $profileId: {{ ($profile_id != null) ? $profile_id : '0' }},
         $profileType: '{{ strtolower($profile_type) }}',
         $displayElement: ['li.fn-remove-avatar']
     });
 })();
</script>
@stop