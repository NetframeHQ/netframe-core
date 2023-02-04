<a 
    href="{{ url()->to('netframe/form-share', ['idNewsfeed' => $post->id]) }}" 
    class="nf-btn fn-netframe-share"
    data-toggle="modal"
    data-target="#modal-ajax"
>
    <span class="btn-img svgicon">
        @include('macros.svg-icons.share')
    </span>
    <span class="btn-txt btn-digit">
        {{ $shareCount }}
    </span>
    <span class="btn-txt">
        {{ trans("netframe.share") }}
    </span>
</a>