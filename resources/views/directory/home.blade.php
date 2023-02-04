@extends('layouts.master-header')

@section('title')
    {{ trans('directory.title') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/css/directory.css') }}">
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.directory')
        </span>
        <h2 class="main-header-title">{{ trans('directory.title') }}</h2>
    </div>

    {{-- CREATE / INVITE USER IF ADMIN --}}
    <ul class="nf-actions nav nav-tabs">
        <li class="nf-action nav-item">
            <a class="nf-btn btn-nobg nav-link active" data-toggle="tab" href="#directory">
                <p class="btn-txt">
                    {{ trans('netframe.usersAll') }}
                </p>
            </a>
        </li>
        <li class="nf-action nav-item">
            <a class="nf-btn btn-nobg nav-link" data-toggle="tab" href="#friends">
                <p class="btn-txt">
                    {{ trans('directory.friend') }}
                </p>
            </a>
        </li>
        @if(session()->has('instanceRoleId') && session('instanceRoleId') <= 2)
            {{-- ••• MENU --}}
            <li class="nf-action">
                <a href="#" class="nf-btn btn-ico btn-submenu">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.plus')
                    </span>
                </a>
                <div class="submenu-container submenu-right">
                    <ul class="submenu">
                        {{-- LINK CREATE USER --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('instance.create') }}">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.plus')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('instances.menu.create') }}
                                </span>
                            </a>
                        </li>
                        {{-- LINK INVITE USER --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('instance.invite') }}">
                                <span class="svgicon btn-img">
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
        @endif
    </ul>
@stop

@section('content')
    <div class="main-container nf-directory">
        <form method="post" class="directory-search">
            <input type="search" placeholder="{{trans('form.search')}}" autocomplete="off" name="q" id="q" class="form-control" />
            <button class="submit">
                <span class="svgicon icon-talkgroup">
                    @include('macros.svg-icons.search')
                </span>
            </button>
        </form>
        <ul class="letters">
            <li><a href="#A" data-val="A">A</a></li>
            <li><a href="#B" data-val="B">B</a></li>
            <li><a href="#C" data-val="C">C</a></li>
            <li><a href="#D" data-val="D">D</a></li>
            <li><a href="#E" data-val="E">E</a></li>
            <li><a href="#F" data-val="F">F</a></li>
            <li><a href="#G" data-val="G">G</a></li>
            <li><a href="#H" data-val="H">H</a></li>
            <li><a href="#I" data-val="I">I</a></li>
            <li><a href="#J" data-val="J">J</a></li>
            <li><a href="#K" data-val="K">K</a></li>
            <li><a href="#L" data-val="L">L</a></li>
            <li><a href="#M" data-val="M">M</a></li>
            <li><a href="#N" data-val="N">N</a></li>
            <li><a href="#O" data-val="O">O</a></li>
            <li><a href="#P" data-val="P">P</a></li>
            <li><a href="#Q" data-val="Q">Q</a></li>
            <li><a href="#R" data-val="R">R</a></li>
            <li><a href="#S" data-val="S">S</a></li>
            <li><a href="#T" data-val="T">T</a></li>
            <li><a href="#U" data-val="U">U</a></li>
            <li><a href="#V" data-val="V">V</a></li>
            <li><a href="#W" data-val="W">W</a></li>
            <li><a href="#X" data-val="X">X</a></li>
            <li><a href="#Y" data-val="Y">Y</a></li>
            <li><a href="#Z" data-val="Z">Z</a></li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane fade in show active" id="directory">
                <ul class="nf-users" id="profiles">
                    @include('directory.list', ['results'=>$results])
                </ul>
            </div>
            <div class="tab-pane fade nf-users" id="friends">
                <ul class="nf-users" id="contacts">
                    @include('directory.friends', ['friends'=>$friends])
                </ul>
            </div>
        </div>
    </div>
    <input type="hidden" name="limit" id="limit" value="15">
@stop



@section('javascripts')
@parent
<script>
$(document).ready(function() {
    tooltip();

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
            var panel = el.closest(".card");

            var dataFriendsId = el.data('tl-delete');

            var jqXhr = $.post("directory/delete-friend" , {
                postData : dataFriendsId
            });

            jqXhr.success(function(data) {
                if(dataFriendsId){
                    panel.fadeOut();
                }
            });
        }
    });
    $('.main-scroller').on('scroll', function(){
        if($(this).has('.directory.show').length>0)
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                var jqXhr = post($('#limit').val(), $('#q').val());
                jqXhr.success(function(data) {
                    $('#profiles').append(data.body);
                    $('#contacts').html(data.friends);
                    $('#limit').val(data.limit);
                    //enable tooltip for new elements
                    tooltip();
                });

            }
    });
    $('#q').on('keyup', function(e){
        var jqXhr = post(0, $('#q').val());
        jqXhr.success(function(data) {
                // alert(JSON.stringify(data));
            $('#profiles').html(data.body);
            $('#contacts').html(data.friends);
            $('#limit').val(15);
            tooltip();
        });
    });
    $('.letters a').on('click', function(e){
        var jqXhr = post(0, $(this).data('val'));
        $('#q').val($(this).data('val'));
        jqXhr.success(function(data) {
            $('#profiles').html(data.body);
            $('#contacts').html(data.friends);
            $('#limit').val(15);
            $('.main-scroller').scrollTop(0,0);
            tooltip();
        });
        return false;
    });
    $('.submit').on('click', function(){
        return false;
    });

});

function post(limit, query){
    var jqXhr = $.post("directory/scroll" , {
        postData : {
            query: query,
            limit: limit
        }
    });
    return jqXhr;
}
//enable tooltip
function tooltip(){
    $(".profiles a.user").tooltip({disabled: true});
    $(".profiles a.user").on({
        'click': function(){
            $(this).tooltip({disabled: false});
            $(this).tooltip({
                track:false,
                open: function( event, ui ) {
                    var elem = $(this);
                    elem.tooltip('option', 'content', "{{trans('form.loading')}}...");
                    var post = $.post("directory/tooltip" , {
                        postData : elem.data('id')
                    });
                    post.success(function(data){
                        elem.tooltip('option','content',data.body);
                    });
                    ui.tooltip.hover(function(){
                        $(this).stop(true).fadeTo(400, 1);
                    });
                    ui.tooltip.mouseleave(function(){
                        $(this).fadeOut(function(){$(this).remove()})
                    });
                }
            });
            $(this).tooltip("open");
            return false;
        },
        'mouseleave': function(){
            $(this).tooltip({disabled: true});
        }
    });
}

</script>
@stop