<a 
    href="{{ url()->to('netframe/form-share-media', ['mediaId' => $media->id]) }}"
    class="nf-btn fn-netframe-share {{ $classCss }}"
    data-toggle="modal"
    data-target="#modal-ajax-comment"
>
    <span class="btn-img svgicon">
        @include('macros.svg-icons.share')
    </span>
    <span class="btn-txt btn-digit">
        {{ $media->share }}
    </span>
    <span class="btn-txt">
        {{ trans("netframe.shared") }}
    </span>
</a>