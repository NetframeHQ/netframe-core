<div class="{{ $class }}">
    <a href="#" class="fn-netframe-like nf-btn btn-nobg {{ ($likeThis) ? 'active' : '' }}" data-tl-like='{{ $dataJsonEncoded }}'>
        <span class="btn-img svgicon icon-like">
            @include('macros.svg-icons.like')
        </span>
        <span class="btn-txt">
            {{ trans('netframe.like') }}
        </span>
    </a>

    @if($nbLike > 0)
        <div class="nf-reacts">
            <div class="nf-react">
                <a class="nf-btn btn-nobg active" href="{{ url()->route('post.likers', ['elementType' => $liked_type, 'elementId' => $liked_id]) }}" data-toggle="modal" data-target="#modal-ajax-thin">
                    <span class="btn-img svgicon icon-like">
                        @include('macros.svg-icons.like')
                    </span>
                    <span class="btn-txt btn-digit">
                        {{ $nbLike }}
                    </span>
                </a>
            </div>
        </div>
    @endif
</div>