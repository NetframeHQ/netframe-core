@if(isset($emoji))
<span class="emoji">
    {{$emoji}}
</span>
@else
<span class="svgicon icon-like">
    @include('macros.svg-icons.like')
</span>
@endif