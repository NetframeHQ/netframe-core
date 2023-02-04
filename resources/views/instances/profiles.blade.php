@extends('instances.main')

@section('title')
    {{ trans('instances.profiles.titles.'.$profileType) }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.parameters') }}</h1>
    </div>
@stop

@section('subcontent')
    <div class="nf-form">
        <div class="nf-settings-title">
            {{ trans('instances.profiles.titles.'.$profileType) }}
            <ul class="nf-actions">
                <li class="nf-action">
                    {{ Form::open(['id' => 'searchProfiles']) }}
                        <div class="nf-input nf-action-search">
                            {{ Form::text('query', '', ['class' => '', 'autocomplete' => 'off', 'placeholder' => trans('form.placeholder.search')]) }}
                            <button class="nf-btn btn-ico" name="search" type="submit">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.search')
                                </span>
                            </button>
                        </div>
                    {{ Form::close() }}
                </li>
                @if($profileType != "users")
                    <li class="nf-action">
                        <a href="{{route($profileEditUrl)}}" class="nf-btn btn-ico">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.plus')
                            </span>
                        </a>
                    </li>
                @else
                    {{--
                    <li class="nf-action">
                        <a href="{{route($profileEditUrl)}}" class="nf-btn btn-submenu">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.plus')
                            </span>
                        </a>
                        <div class="submenu-container submenu-right">
                            <ul class="submenu">
                                <li>
                                    <a class="nf-btn" href="{{route($profileAddUserUrl)}}">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.plus')
                                        </span>
                                        <span class="btn-txt">
                                            {{ trans('instances.menu.create') }}
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="nf-btn" href="{{route($profileInviteUserUrl)}}">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.plus')
                                        </span>
                                        <span class="btn-txt">
                                            {{ trans('instances.menu.invite') }}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    --}}
                @endif

            </ul>
        </div>
        <ul class="nf-list-settings">
            @foreach($profiles as $profile)
                @include('join.member-card', ['profile' => $instance, 'member' => $profile])
            @endforeach
        </ul>
    </div>

    <div class="nf-pagination">
        @if($fromSearch == 0)
            {{ $profiles->links('vendor.pagination.bootstrap-4') }}
        @endif
    </div>

    @if($profileType != "users")
        <a href="{{route($profileEditUrl)}}" class="nf-btn btn-full btn-xxl">
            <span class="btn-img svgicon">
                @include('macros.svg-icons.plus')
            </span>
            <span class="btn-txt">
                {{trans('instances.profiles.create-'.$profileType)}}
            </span>
        </a>
    @endif
@stop

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
            var targetReplace = $(el.closest('.fn-right-management').data('target-return'));
            targetReplace.fadeOut('slow', function () {
                targetReplace.replaceWith(data.view);
                targetReplace.fadeIn('slow');
            });
        }).error(function(xhr) {

        });
    });
    $( document ).ajaxSuccess(function( event, xhr, settings ) {
        if(xhr.responseJSON.errors){
            $('.validatedForm').find('.error').text("")
            $('.validatedForm').find('.is-invalid').removeClass("is-invalid")
            let errors = xhr.responseJSON.errors
            for (let [key, value] of Object.entries(errors)) {
                $('input[name='+key+']').addClass('is-invalid')
                $('input[name='+key+']').parent().next().text(`${value}`)
            }
        }else if(xhr.responseJSON.success){
            $('#user-'+xhr.responseJSON.success).text(xhr.responseJSON.name);
        }
    });
})(jQuery);
</script>
@stop