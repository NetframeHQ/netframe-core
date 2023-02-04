<div class="{{ $class }} {{ ($likeThis) ? 'active' : '' }}">
    <div class="nf-post-actions">
        <a href="#" class="fn-netframe-like nf-btn" data-tl-like='{{ json_encode($dataJsonEncoded) }}'>
            <span class="btn-img fn-reaction">
                @if(isset($emoji))
                <span class="emoji">
                    {{$emoji}}
                </span>
                @else
                <span class="svgicon">
                    @include('macros.svg-icons.like')
                </span>
                @endif
            </span>
            <span class="btn-txt">
                {{ trans('netframe.like') }}
            </span>
        </a>

        <ul class="btn-react fn-like-reactions">
            @foreach($customLikesEmojis as $emoji)
                <li><a href="#" class="fn-netframe-like" data-tl-like="{{json_encode(array_merge($dataJsonEncoded,['emojis_id'=>$emoji->id]))}}">{{$emoji->value}}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="nf-post-reacts">
        @include('macros.like-reactions')
    </div>
</div>
