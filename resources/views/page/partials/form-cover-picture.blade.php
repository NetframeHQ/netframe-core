

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

@section('javascripts')
@parent
<script>
(function () {
     var attachmentSystem = $('#profile-picture');
     new AttachmentSystem({
         $wrapper: attachmentSystem,
         $previewDomClass: '.profile-image img',
         $fileUpload: attachmentSystem.find('#fileupload'),
         $profileMedia: 1,
         $postMedia: 0,
         $confidentiality: 0,
         $profileId: {{ $profile_id }},
         $profileType: '{{ strtolower($profile_type) }}',
     });
 })();
</script>
@stop