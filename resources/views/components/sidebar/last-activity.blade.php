@if(count($expert_action) > 0)
    <section id="widget-last-activity" class="block-widget">
            @if(isset($routeMore))
        <a href="{{ $routeMore }}" class="btn-xs btn-default float-right modal-hidden" data-toggle="modal" data-target="#modal-ajax">{{ trans('netframe.viewAll') }}</a>
            @endif
        <h2 class="widget-title">{{ trans('angel.expertActions') }}</h2>

        @foreach($expert_action as $action)
            @include("page.type-content.netframe-actions", ['Taction'=>$action])
        @endforeach
    </section>
@endif