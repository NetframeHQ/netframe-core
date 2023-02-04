<a href="#" class="fn-netframe-pintop nf-btn btn-nobg btn-anim" data-tl-pintop='{{ $dataJsonEncoded }}'>
    <span class="btn-img svgicon">
        @if(!$pinned)
            @include('macros.svg-icons.pin')
        @else
            @include('macros.svg-icons.pinned')
        @endif
    </span>
    <span class="btn-txt">
        @if(!$pinned)
            {{ trans('netframe.pintop') }}
        @else
            {{ trans('netframe.unpintop') }}
        @endif
    </span>
</a>
