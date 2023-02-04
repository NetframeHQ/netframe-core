<?php
$testProfile = null;
?>

@foreach($feed AS $message)
    @if($testProfile != $message->sender_id.'-'.$message->sender_type)
        <?php
            $testProfile = $message->sender_id.'-'.$message->sender_type;
            $displayThumb = true;
        ?>
    @else
        <?php
            $displayThumb = false;
        ?>
    @endif
    <article data-message-id="{{ $message->id }}" class="{{ ($myProfile == get_class($message->sender).'-'.$message->sender->id) ? 'text-right to' : '' }}">
        {!! HTML::thumbImage(
            $message->sender->profile_media_id,
            30,
            30,
            [],
            $message->sender->getType(),
            'avatar ' . $message->sender->getType(),
            $message->sender
        ) !!}
        <div class="mailMessage {{ ($myProfile == get_class($message->sender).'-'.$message->sender->id) ? 'to' : 'from' }}">
            {!! \App\Helpers\StringHelper::formatPostText($message->content) !!}
        </div>
        <div class="author">
            @if($displayThumb)
                <strong>{{ $message->sender->getNameDisplay() }}</strong>
            @endif
            <time class="datetime">
                {{ \App\Helpers\DateHelper::messageDate($message->created_at) }}
            </time>
        </div>
    </article>
@endforeach
