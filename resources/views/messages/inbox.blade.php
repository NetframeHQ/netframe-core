@extends('layouts.master-header')

@section('favicon')
  {{url()->route('netframe.svgicon', ['name' => 'inbox'])}}
@endsection

@section('title')
  {{ trans('messages.inbox') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
  <div class="main-header-infos">
    <span class="svgicon icon-talkgroup">
        @include('macros.svg-icons.inbox')
    </span>
    <h2 class="main-header-title">
      <a href="{{ url()->route('messages_inbox') }}">
        {{ trans('messages.inbox') }}
      </a>
    </h2>
  </div>
@endsection

@section('content')
<div id="nav_skipped" class="main-scroller">
    <div class="inbox">
        <div class="inbox-container">
            <div class="inbox-list" id="messages-list">
                    @if(count($messagesGroups) > 0)
                        @include('messages.partials.feed-list')
                    @else
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {{ trans('messages.inboxEmpty') }}
                                </div>
                            </div>
                        </div>
                    @endif
            </div>
            <div class="inbox-messages d-none d-sm-block" id="messages-feed">

            </div>
        </div>

        <div class="xs-screen d-block d-sm-none"></div>
    </div>
</div>
@stop

@section('javascripts')
@parent
<script>
(function($){

    var xs_screen = false;
    var feedId = 0;

    $(document).on('click', '.fn-new-message', function(e){
        e.preventDefault();
        //load form with ajax in main column
        $.get('{{ url()->to('/') }}' + laroute.route('new_messages'))
            .success(function (data) {
                $('#messages-feed').html(data.view);
                if(!$('#messages-feed').is(':visible')){
                    $('#messages-list').addClass('d-none d-sm-block');
                    $('#messages-feed').removeClass('d-none d-sm-block');
                }
        });
    });

    $(document).on('click', '.fn-load-inbox', function(e){
        e.preventDefault();
        $('#messages-list').removeClass('d-none d-sm-block');
        $('#messages-feed').addClass('d-none d-sm-block');
    });

    $(document).on('click', '.fn-load-message-feed', function(e){
        var el = $(this);
        $('.fn-load-message-feed').each(function(){
            $(this).removeClass('active');
        })
        el.addClass('active');

        //load message feed in main column
        feedId = el.data('group');
        var params = {
            feedId: feedId
        };

        $.get('{{ url()->to('/') }}' + laroute.route('messages_feed', params))
            .success(function (data) {
                if(data.view != undefined){
                    $('#messages-feed').html(data.view);
                    // scroll to bottom of div
                    $('.mailMessages').animate({ scrollTop: $('.mailMessages').prop('scrollHeight')}, 500);

                    feedId = el.data('group');
                    if(!$('#messages-feed').is(':visible')){

                        $('#messages-list').addClass('d-none d-sm-block');
                        $('#messages-feed').removeClass('d-none d-sm-block');
                    }
                    top.location = "#message-feed";
                    setTimeout(function(){
                        historyTab = [['#messages-list', 'removeClass', 'hidden-xs'], ['#messages-feed', 'addClass', 'hidden-xs']];
                    }, 500);

                    // detect top feed to reload messages
                    $('.mailMessages').scroll(function(){
                        let scrollTopDiv = $('.mailMessages').scrollTop();
                        console.log(scrollTopDiv);
                        if (scrollTopDiv == 0) {
                            inboxInfinite();
                        }
                    });
                }
            });
    });

    $(document).on('submit', '#form-post-message', function(e){
        e.preventDefault();

        var _form = $(this);
        var actionUrl = _form.attr('action');
        var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                _form.replaceWith(data.formView);
                if(typeof data.success != 'undefined') {
                    $('.messages-feed').append(data.messageView);
                    $('.messages-feed').scrollTop('100000');
                    $('#form-textarea-answer').val('');
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    });

    $(document).on('click', '.fn-message-feed', function(e){
        e.preventDefault();
        actionUrl = $(this).attr('href');
        $.ajax({
            url: actionUrl,
            type: "GET",
            success: function( data ) {
                $('.messages-feed .content').prepend(data.view);
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    });

 // include function for infinitescroll
    var scrolling = 0;

    function inboxInfinite(){
        var lastMessageId = $('#messages-feed .mailMessages article').first().data('message-id');
        var params = {
            feedId: feedId,
            lastId: lastMessageId
        };
        $.get('{{ url()->to('/') }}' + laroute.route('messages_feed', params))
        .success(function (data) {
            if(data.view != ''){
                $("#messages-feed .mailMessages").prepend(data.view);
                scrolling = 0;
            }
        });
    }


})(jQuery)
</script>
@stop