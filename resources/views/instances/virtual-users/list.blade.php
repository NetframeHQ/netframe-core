@extends('instances.main')

@section('title')
    {{ trans('instances.virtualUsers.title') }} {{ $user->getNameDisplay() }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.parameters') }}</h1>
    </div>
@endsection

@section('subcontent')
    <div class="nf-form d-block">
        <div class="nf-settings-title">
            {{ trans('instances.virtualUsers.title') }} {{ $user->getNameDisplay() }}
        </div>
        <div class="mt-2 mb-2 ml-3 pt-1">
            {{ trans('instances.virtualUsers.explain') }}
        </div>
        <div class="pl-2 pb-2">
            <a class="nf-btn btn-ico " href="{{ url()->route('instance.virtualuser.edit', ['userId' => $user->id]) }}">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.add-user')
                </span>
            </a>
        </div>

        <ul class="nf-list-settings">
            @foreach($virtualUsers as $virtualUser)
                <li class="nf-list-setting force-hover virtualUser-{{ $virtualUser->id }} @if($virtualUser->active == 0) disabled @endif">
                    <div class="nf-list-infos">
                        <div class="nf-list-title" id="user-{{$virtualUser->id}}">
                            {{ $virtualUser->getNameDisplay() }}
                        </div>
                        <span class="nf-list-subtitle">
                            {{ trans('instances.profiles.createdAt') }} : {{ \App\Helpers\DateHelper::feedDate($virtualUser->created_at) }}
                        </span>
                    </div>
                    <ul class="nf-actions">
                        @if($virtualUser->active == 0)
                            <li class="nf-action">
                                <div class="error nf-lbl">
                                    <span class="lbl-txt">
                                        {{ trans('instances.profiles.disable') }}
                                    </span>
                                </div>
                            </li>
                        @else
                        <li class="nf-action">
                            <a href="#" class="nf-btn btn-submenu btn-ico">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.menu')
                                </span>
                            </a>
                            <div class="submenu-container submenu-right">
                                <ul class="submenu">
                                    <li>
                                        <a class="nf-btn" href="{{url()->route('instance.virtualuser.edit', ['userId' => $user->id, 'virtualUserId' => $virtualUser->id])}}">
                                            <span class="btn-txt">
                                                {{ trans('instances.profiles.change.edit') }}
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        @if($virtualUser->active == 1)
                                            <a class="nf-btn fn-active-profile" href="{{ URL::route('instance.virtualuser.disable') }}" data-toggle-state="0" data-profile-id="{{ $virtualUser->id }}">
                                                <span class="btn-txt">
                                                    {{ trans('instances.profiles.disable') }}
                                                </span>
                                            </a>
                                        @else
                                            <a class="nf-btn fn-active-profile" href="{{ URL::route('instance.virtualuser.disable') }}" data-toggle-state="1" data-profile-id="{{ $virtualUser->id }}">
                                                <span class="btn-txt">
                                                    {{ trans('instances.profiles.enable') }}
                                                </span>
                                            </a>
                                        @endif
                                    </li>
                                    <li>
                                        <a class="nf-btn fn-confirm-delete" href="{{ URL::route('instance.virtualuser.delete', ['virtualUserId' => $virtualUser->id]) }}" data-txtconfirm="{{ trans('instances.virtualUsers.deleteConfirm') }}">
                                            <span class="btn-txt">
                                                {{ trans('instances.profiles.delete') }}
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

@section('javascripts')
@parent
<script>
var disableTxt = '{{ trans('instances.profiles.disable') }}';
var enableTxt = '{{ trans('instances.profiles.enable') }}';

(function($){
    $(document).on('click', '.fn-active-profile', function(e){
        e.preventDefault();
        var el = $(this);
        var link = el.attr('href');
        var newState = el.data('toggle-state');
        var profileId = el.data('profile-id');
        var params = {
            stateTo: newState,
            profileId: profileId
        };

        var jqXhr = $.post(link, params);
        jqXhr.success(function(data) {
            if(data.active == 1){
                el.removeClass('btn-danger');
                el.addClass('btn-success');
                el.data('toggle-state', 0);
                el.find('span.btn-txt').html(disableTxt);
                el.closest('.nf-list-setting').removeClass('disabled');
            }
            else if(data.active == 0){
                el.removeClass('btn-success');
                el.addClass('btn-danger');
                el.data('toggle-state', 1);
                el.find('span.btn-txt').html(enableTxt);
                el.closest('.nf-list-setting').addClass('disabled');
            }
        }).error(function(xhr) {
        });
    });
})(jQuery);
</script>
@stop