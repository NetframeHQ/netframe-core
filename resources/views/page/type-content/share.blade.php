<?php $medias = $post->post->medias; ?>
@if($post->content !== '')
    <div class="panel-share">
        {!! \App\Helpers\StringHelper::formatPostText($post->content) !!}
    </div>
@endif
@include('page.type-content.share-preview')
