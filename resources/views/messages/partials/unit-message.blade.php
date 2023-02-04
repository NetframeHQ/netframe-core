<article data-message-id="{{ $message->id }}" class="text-right to">
    {!! HTML::thumbImage(
        $message->sender->profile_media_id,
        30,
        30,
        [],
        $message->sender->getType(),
        'avatar ' . $message->sender->getType(),
        $message->sender
    ) !!}
    <div class="mailMessage to">
        {!! \App\Helpers\StringHelper::formatPostText($message->content) !!}
    </div>
    <div class="author">
        <strong>{{ $message->sender->getNameDisplay() }}</strong>
        <time class="datetime">
            {{ \App\Helpers\DateHelper::messageDate($message->created_at) }}
        </time>
    </div>
</article>