@extends('layouts.master-header')

@section('title')
    {{ trans('search.results_for') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    @parent
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap/bootstrap-slider.min.css') }}">
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @if (request()->section)
                @include('macros.svg-icons.'.$sections[request()->section]['iconBig'])
            @else
                @include('macros.svg-icons.search_big')
            @endif
        </span>
        <h1 class="main-header-title">
            @if (request()->section)
                {{ trans('search.results_title.'.request()->section) }}
            @else
                {{ trans('search.results_for') }}
            @endif
            "<em>{{ $query->term() }}</em>"
        </h1>
        @if ($results != null)
            <p>{{ $total }} {{ trans('search.results') }} ({{ $results['took']/1000 }} sec.)</p>
        @endif
    </div>

    {{-- CUSTOM LINKS PAGE SETTINGS USER --}}
    <ul class="nf-actions">
        {{-- ••• MENU --}}
        <li class="nf-action nf-custom-nav">
            <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.filters')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    {{-- TOUS LES RESULTATS --}}
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['types' => null, 'page' => null, 'section' => null]) }}" class="nf-btn @if (!request()->section) {{ 'nf-customactive btn-nohov' }} @endif">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.search')
                            </span>
                            <span class="btn-txt">
                                {{ trans('search.menu.all') }}
                            </span>
                        </a>
                    </li>
                    {{-- RESULTATS PAR TYPE --}}
                    @foreach ($sections as $section => $params)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['types' => $params['types'], 'page' => null, 'section' => $section]) }}" class="nf-btn @if ($section===request()->section) {{ 'nf-customactive btn-nohov' }} @endif">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.'.$params['icon'])
                                </span>
                                <span class="btn-txt">
                                    {{ trans($params['i18n']) }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </li>
    </ul>

@stop

@section('content')
    <div id="nav_skipped" class="main-scroller nf-search" id="fn-result-view">
        {{--
        <ul>
            <li><a href="{{ request()->fullUrlWithQuery(['types' => null, 'page' => 1, 'section' => null]) }}">Tous</a></li>
            @foreach ($sections as $section => $params)
                <li>
                    <a href="{{ request()->fullUrlWithQuery(['types' => $params['types'], 'section' => $section]) }}">{{ $section }}</a>
                </li>
            @endforeach
        </ul>
        --}}

        <!-- PLACEHOLDER -->

        <section class="nf-search-list @if (!$groupBySections) {{ 'nf-search-single' }} @endif">

            {{-- RESULTATS GROUPÉS PAR SECTIONS --}}
            @if ($groupBySections)
            @foreach($resultsBySection as $section => $sectionResults)
                <div class="nf-search-results {{ $section }}">

                    @if (!is_null($sectionResults) && $sectionResults['hits']['total'] > 0)

                    @foreach($sectionResults['hits']['hits'] as $result)
                        {{-- AFFICHE UN RÉSULTAT --}}
                        @include('search.result')
                    @endforeach

                    @else
                        <div class="nf-search-placeholder">
                        {{ trans("search.no_result_in") }} {{ trans($sections[$section]['i18n']) }}
                        </div>
                    @endif

                    @if (!is_null($sectionResults) && $sectionResults['hits']['total'] > $sections[$section]['nbResultsMax'])
                        <div class="nf-search-result nf-search-more">
                            <a class="nf-btn" href="{{ url()->route('search', ['term' => $query->term(), 'types' => $sections[$section]['types'], 'section' => $section]) }}">
                                <span class="btn-txt">{{ trans('netframe.viewAll') }}</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- LISTE DE RÉSULATS POUR UNE SECTION --}}
            @else
                <div class="nf-search-results">
                @if ($results != null && $results['hits']['total'] > 0)
                    @foreach($results['hits']['hits'] as $result)
                        {{-- AFFICHE UN RÉSULTAT --}}
                        @include('search.result')
                    @endforeach
                @else
                    <div class="nf-search-placeholder">
                        {{ trans("search.no_matching_results") }}
                    </div>
                @endif
                </div>
            @endif


            @if (!$groupBySections)
                <ul class="nf-actions nf-paginations">
                    @if ($pagination['current'] > 1)
                        <li class="nf-action">
                            <a class="nf-btn" href="{{ request()->fullUrlWithQuery(['page' => $pagination['current']-1]) }} ">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.chevron_left')
                                </span>
                                <span class="btn-txt">{{ trans('search.pagination.prev') }}</span>
                            </a>
                        </li>
                    @endif
                    @for (($i = $pagination['total'] > 20 && $pagination['current'] > 20 ? $pagination['current']-10 : 1); $i <= ($pagination['total'] > 20 ? $pagination['current']+10 : $pagination['total']); $i++)
                        <li class="nf-action">
                            <a class="nf-btn @if ($i===$pagination['current']) {{ 'active' }} @endif" href="{{ request()->fullUrlWithQuery(['page' => $i]) }} ">
                                <span class="btn-txt">{{ $i }}</span>
                            </a>
                        </li>
                    @endfor
                    @if ($pagination['current'] < $pagination['total'])
                        <li class="nf-action">
                            <a class="nf-btn" href="{{ request()->fullUrlWithQuery(['page' => $pagination['current']+1]) }} ">
                                <span class="btn-txt">{{ trans('search.pagination.next') }}</span>
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.chevron_right')
                                </span>
                            </a>
                        </li>
                    @endif
                </ul>
            @endif

        </section>
    </div>
@stop

@section('javascripts')
@parent
<script>

(function($){
    //for modal media view
    var $modal = $('#viewMediaModal');
    playMediaModal = new PlayMediaModal({
        $modal: $modal,
        $modalTitle: $modal.find('.modal-title'),
        $modalContent: $modal.find('.modal-carousel .carousel-item'),
        $media: $('.viewMedia'),
        baseUrl: baseUrl
    });
})(jQuery);
</script>
@stop