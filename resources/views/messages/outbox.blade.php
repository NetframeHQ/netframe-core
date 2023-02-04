@extends('layouts.page')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-2">
        <div class="row">
            <div class="col-md-12 col-lg-12 panel-border-bottom">
                <h1>{{ trans('messages.outbox') }}</h1>
                <a href="{{ url()->route('messages_inbox') }}" title="">
                    {{ trans('messages.back') }}
                </a>
                <br /><br />

                    @include('components.messages.new-with-list')

                    @if(count($messages) > 0)
                        @foreach($messages as $message)
                            <?php  $message = $message['msgObject']; ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row mg-bottom">
                            <div class="col-xs-3 col-md-3 col-sm-3">
                                @include('components.thumbs.display', ['profile' => $message->receiver, 'profileType' => $message->receiver->getType()])
                            </div>

                            <div class="col-xs-9 col-md-9 col-sm-9">
                                <time class="datetime">
                                    {{ date("d / m / Y", strtotime($message->updated_at)) }}
                                </time>


                                <div class="message-content">
                                    {{ \App\Helpers\StringHelper::formatPostText($message->content) }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6 col-md-6">
                                <a href="{{ url()->route('messages_feed',['feedId' => $message->messages_mail_group_id]) }}" class="btn btn-border-default" title="{{ trans('messages.viewAll') }}">
                                    {{ trans('messages.viewAll') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                        @endforeach
                    @else
                <div class="card playlistItem">
                    <div class="card-body">
                        <div class="row">
                            {{ trans('messages.outboxEmpty') }}
                        </div>
                    </div>
                </div>
                    @endif
            </div>
        </div>
    </div>
</div>
@stop
