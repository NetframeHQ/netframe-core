@if (isset($profiles) && count($profiles) > 0)
<section id="widget-profile-mosaic-{{ $profileType }}" class="block-widget">
    @if(isset($routeMore))
    <a href="{{ $routeMore }}" class="btn-xs btn-default float-right modal-hidden" data-toggle="modal" data-target="#modal-ajax">{{ trans('netframe.viewAll') }}</a>
    @endif
    <h2 class="widget-title">{{ trans('widgets.'.$prefixTranslate.$profileType) }}</h2>
    {!! HTML::thumbMosaic($profiles) !!}
</section>
@endif