@extends('instances.main')

@section('title')
    {{ trans('stats.title') }} • {{ $globalInstanceName }}
@stop

@section('subcontent')
    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('stats.title') }}
            {{ trans('stats.from') }} {{ date('d/m/Y', strtotime($startPeriod)) }} {{ trans('stats.to') }} {{ date('d/m/Y', strtotime($endPeriod)) }}
        </div>
    </div>

    <div class="nf-form justify-content-center pt-3 pb-3">
        <div class="btn-group" role="group" aria-label="{{ trans('stats.period') }}">
            <a href="{{ url()->route('instance.stats', ['period' => 7]) }}" class="btn button @if($period == 7) primary @endif">{{ trans('stats.7days') }}</a>
            <a href="{{ url()->route('instance.stats', ['period' => 30]) }}" class="btn button @if($period == 30) primary @endif">{{ trans('stats.30days') }}</a>
            <a href="{{ url()->route('instance.stats', ['period' => 365]) }}" class="btn button @if($period == 365) primary @endif">{{ trans('stats.1year') }}</a>
        </div>
    </div>

    <div class="nf-form">
        <div class="nf-settings-title small-title">
            {{ trans('stats.users') }}
        </div>
        <div class="nf-form-informations">
            <div class="row">
                <div class="col-12 col-md-4 text-center">
                    {{ trans('stats.total') }} : {{ $users }}
                </div>
                <div class="col-12 col-md-4 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newUsers }}
                    {!! HTML::statsIncrease($newUsers, $newPreviewUsers) !!}
                </div>
                <div class="col-12 col-md-4 text-center">
                    {{ trans('stats.connexions') }} : {{ $connexions }}
                    {!! HTML::statsIncrease($connexions, $previewConnexions) !!}
                </div>
            </div>
        </div>
        @if(count($topUsers) > 0)
            <div class="nf-form-informations">
                <div class="text-center mb-4"><strong>{{ trans('stats.topUsers') }}</strong></div>
                <ul class="nf-list-settings">
                    @foreach($topUsers as $user)
                        @include('join.member-card', ['profile' => $instance, 'member' => $user, 'fromStats' => true])
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="nf-form">
        <div class="nf-settings-title small-title">
            @if($monoProfile)
                {{ trans('stats.groups') }}
            @else
                {{ trans('stats.groupsOld') }}
            @endif
        </div>

        @if($monoProfile)
            <div class="nf-form-informations">
                <div class="row">
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.total') }} : {{ $groups }}
                    </div>
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.onPeriod') }} : {{ $newGroups }}
                        {!! HTML::statsIncrease($newGroups, $newPreviewGroups) !!}
                    </div>
                </div>
            </div>
        @else
            <div class="nf-form-informations">
                <div class="text-center mb-4"><strong>{{ trans('stats.houses') }}</strong></div>
                <div class="row">
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.total') }} : {{ $houses }}
                    </div>
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.onPeriod') }} : {{ $newHouses }}
                        {!! HTML::statsIncrease($newHouses, $newPreviewHouses) !!}
                    </div>
                </div>
            </div>
            <div class="nf-form-informations">
                <div class="text-center mb-4"><strong>{{ trans('stats.communities') }}</strong></div>
                <div class="row">
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.total') }} : {{ $groups }}
                    </div>
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.onPeriod') }} : {{ $newGroups }}
                        {!! HTML::statsIncrease($newGroups, $newPreviewGroups) !!}
                    </div>
                </div>
            </div>
            <div class="nf-form-informations">
                <div class="text-center mb-4"><strong>{{ trans('stats.projects') }}</strong></div>
                <div class="row">
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.total') }} : {{ $projects }}
                    </div>
                    <div class="col-12 col-md-6 text-center">
                        {{ trans('stats.onPeriod') }} : {{ $newProjects }}
                        {!! HTML::statsIncrease($newProjects, $newPreviewProjects) !!}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="nf-form">
        <div class="nf-settings-title small-title">
            {{ trans('stats.content') }}
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.news') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $news }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newNews }}
                    {!! HTML::statsIncrease($newNews, $newPreviewNews) !!}
                </div>
            </div>
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.events') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $events }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newEvents }}
                    {!! HTML::statsIncrease($newEvents, $newPreviewEvents) !!}
                </div>
            </div>
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.offers') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $offers }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newOffers }}
                    {!! HTML::statsIncrease($newOffers, $newPreviewOffers) !!}
                </div>
            </div>
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.medias') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $medias }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newMedias }}
                    {!! HTML::statsIncrease($newMedias, $newPreviewMedias) !!}
                </div>
            </div>
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.channelsPosts') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $channelsPosts }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newNews }}
                    {!! HTML::statsIncrease($newChannelsPosts, $newPreviewChannelsPosts) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="nf-form">
        <div class="nf-settings-title small-title">
            {{ trans('stats.reactions') }}
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.likes') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $likes }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newLikes }}
                    {!! HTML::statsIncrease($newLikes, $newPreviewLikes) !!}
                </div>
            </div>
        </div>
        <div class="nf-form-informations">
            <div class="text-center mb-4"><strong>{{ trans('stats.comments') }}</strong></div>
            <div class="row">
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.total') }} : {{ $comments }}
                </div>
                <div class="col-12 col-md-6 text-center">
                    {{ trans('stats.onPeriod') }} : {{ $newComments }}
                    {!! HTML::statsIncrease($newComments, $newPreviewComments) !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascripts')
@parent
{{ HTML::script('/packages/chart.js/chart.js.js?v=' . env('ASSETS_VERSION', 5)) }}
<script>

</script>
@stop