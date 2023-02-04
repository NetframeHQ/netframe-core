@extends('layouts.page')

@section('title')
    {{ trans('friends.results') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/search_results.css') }}">
@stop

@section('content')
<div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.user')
        </span>
        <h2 class="main-header-title">
            {{ trans('friends.results') }}
            @if(0 === count($results))
                {{ trans('friends.no_matching_results') }}
            @endif
        </h2>
        <p>

    </div>
</div>

<div class="main-container">
    <div id="nav_skipped" class="main-scroller">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="row">
                    <div class="col-md-12 col-lg-12 contacts-list">
                        @foreach($results as $friend)
                        <div class="card">
                            <div class="card-body">
                                <a href="{{ \App\Helpers\StringHelper::uriHomeUserObject($friend) }}" class="float-left">

                                    @if($friend->profileImage != null)
                                        {!! HTML::thumbnail($friend->profileImage, '40', '40', array('class' => 'img-fluid'), asset('assets/img/avatar/user.jpg')) !!}
                                    @else
                                        <span class="svgicon">
                                            @include('macros.svg-icons.user')
                                        </span>
                                    @endif
                                </a>
                                <div class="float-left  mg-left-10">
                                    <p>
                                        <a href="{{ \App\Helpers\StringHelper::uriHomeUserObject($friend) }}">{{ $friend->firstname }} {{ $friend->name }}</a>
                                    </p>
                                </div>
                                {{--
                                <div class="float-left  mg-left-10">
                                    <a href="{{ url()->route('channels.messenger', ['userId' => $friend->id]) }}" data-toggle="modal"
                                        data-target="#modal-ajax">
                                        <span class="svgicon icon-talk float-left">
                                            @include('macros.svg-icons.talk')
                                        </span>
                                        {{ trans('page.leave_message') }}
                                    </a>
                                </div>
                                --}}
                                <div class="float-right">
                                    {!! HTML::deleteFriendBtn(['friend_id' => $friend->id,'users_id'  => auth()->guard('web')->user()->id]) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('sidebar')
    @include('components.sidebar-user')
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function() {

  //--------------------- DELETE FRIEND FUNCTION
    $(document).on('click', '.fn-delete-friend', function(e) {
        var _confirm = confirm('{{ trans('friends.confirmDelete') }}');

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
        else{
            e.preventDefault();
            var el = $(this);
            var panel = el.closest(".panel");

            var dataFriendsId = el.data('tl-delete');

            var jqXhr = $.post("delete-friend" , {
                postData : dataFriendsId
            });

            jqXhr.success(function(data) {
                if(dataFriendsId){
                    panel.fadeOut();
                }
            });
        }
    });

});

</script>
@stop

