@if (count($profiles) > 0)
<section id="widget-follower class="block-widget">
    <h2 class="widget-title">{{ trans('netframe.followers') }}</h2>
    {{ HTML::thumbMosaic($profiles) }}
</section>
@endif