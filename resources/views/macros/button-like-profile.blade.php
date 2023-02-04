<div class="nf-btn btn-xl profile-like @if($liked) active @endif @if($hideLike) d-none @endif" >
    <span class="svgicon icon-like btn-img">
        @include('macros.svg-icons.like')
    </span>
    <span class="btn-txt">
        {{ trans('netframe.like') }}
    </span>
    <button class="like-number btn-label" href="{{ url()->route('post.likers', ['elementType' => $liked_type, 'elementId' => $liked_id]) }}" data-toggle="modal" data-target="#modal-ajax-thin">
        {{ $nbLike }}
    </button>
    <button class="nf-invisiblink fn-like-profile" data-tl-like='{{ $dataJsonEncoded }}' />
</div>
