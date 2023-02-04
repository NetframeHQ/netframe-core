<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('messages.feed') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <div class="row">
        @if(isset($totalMessages) && $totalMessages > $nbDisplayed)
            <p class="text-center">
                <a href="{{ url()->route('project.inbox', ['idProject' => $project->id, 'full' => 1 ] ) }}" class="btn btn-border-default float-right fn-reload-modal">
                    {{ trans('messages.previousMessages') }}
                </a>
            </p>
        @endif


            @foreach($feed AS $message)

                @if($type == 5 && $message->sender_id == auth()->guard('web')->user()->id && $message->sender_type == 'User' )
            <div class="block-media-comment message-to col-md-8 offset-md-4">
                <div class="message-heading text-right">
                    <time datetime="{{ $message->updated_at }}" class="datetime-sm">
                        {{ date("d/m/Y - H:i:s", strtotime($message->updated_at)) }}
                    </time>
                </div>
                <div class="bubble-message">
                    <div class="message-text">
                        {{ $message->content }}
                    </div>
                </div>
            </div>
                @else
            <div class="block-media-comment message-from col-md-8">
                <div class="message-heading">
                    <time datetime="{{ $message->updated_at }}" class="datetime-sm">
                        {{ date("d/m/Y - H:i:s", strtotime($message->updated_at)) }}
                    </time>
                </div>
                <div class="bubble-message">
                    <p class="message-pseudo">{{ $message->sender->getNameDisplay() }}</pp>
                    <div class="message-text">
                        {{ $message->content }}
                    </div>
                </div>
            </div>
                @endif
            @endforeach

    </div>
</div>
<!-- End MODAL-BODY -->

<div class="modal-footer">
    <button type="button" class="btn btn-border-default" data-dismiss="modal">{{ trans('form.close') }}</button>
</div>
<!-- End MODAL-FOOTER -->

<script>
(function($){
    $(document).on('click', '.fn-reload-modal', function(e){
        e.preventDefault();
        var params = {
                idProject: '{{ $project->id }}',
                full: 1
            };
        $.get('{{ url()->to('/') }}' + laroute.route('project.inbox', params))
        .success(function (data) {
            $('#modal-ajax').find('.modal-content').html(data);
        });
    });
})(jQuery);

</script>
