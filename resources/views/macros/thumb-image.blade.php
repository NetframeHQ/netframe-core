@if ($mediaId != null)
    @if($spanStyle != null)
        <span class="{{ $spanStyle }}">
    @endif
    <div class="nf-thumbnail {{$defaultSrc}}" style="background-image:url({{ url()->route('media_download', array('id' => $mediaId, 'thumb' => 1)) }})" {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
    @if($spanStyle != null)
        </span>
    @endif
@elseif(in_array($defaultSrc, ['user', 'user_big']) && $profile != null)
    {!! HTML::userAvatar($profile, $width, $spanStyle) !!}
@else
    <span class="svgicon {{$spanStyle}}">
        @include('macros.svg-icons.'.$defaultSrc)
    </span>
@endif
