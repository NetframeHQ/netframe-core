<a 
    href="{{ url()->to('netframe/form-share-playlist', ['playlistId' => $playlist->id]) }}"
    class="nf-btn fn-netframe-share {{ $classCss }}"
    data-toggle="modal"
    data-target="#modal-ajax-comment"
>
    <span class="btn-img svgicon icon-share">
        @include('macros.svg-icons.share')
    </span>
    <span class="btn-txt btn-digit">
        {{ $playlist->share }}
    </span>
    <span class="btn-txt">
        {{ trans("netframe.share") }}
    </span>
</a>