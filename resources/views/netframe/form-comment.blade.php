<div class="modal-body modal-share">
    <div class="modal-header">
        <h4 class="modal-title">
            {{ trans('netframe.yourComment') }}
        </h4>
        <a class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">{{trans('form.close') }}</span>
        </a>
    </div>

    {{ Form::open(['url'=> '/netframe/comment-publish', 'id' => 'form-comment-page', 'files' => true]) }}
        <div id="publish-as-hidden-cm">
            @if(isset($comment->id) or isset($inputOld['comment_id']))
                {{ Form::hidden("comment_id", (isset($inputOld['comment_id']) ? $inputOld['comment_id'] : $comment->id) ) }}
                {{ Form::hidden("author_id", (isset($inputOld['author_id']) ? $inputOld['author_id'] : $comment->author_id) ) }}
                {{ Form::hidden("author_type", (isset($inputOld['author_type']) ? $inputOld['author_type'] : strtolower(class_basename($comment->author_type))) ) }}
            @else
                {{ Form::hidden("author_id", (isset($inputOld['author_id']) ? $inputOld['author_id'] : auth()->guard('web')->user()->id) ) }}
                {{ Form::hidden("author_type", (isset($inputOld['author_type']) ? $inputOld['author_type'] : 'user') ) }}
            @endif
        </div>

        {{ Form::hidden("post_id", (isset($inputOld['post_id']) ? $inputOld['post_id'] : $post->id) ) }}
        {{ Form::hidden("post_type", (isset($inputOld['post_type']) ? $inputOld['post_type'] : get_class($post)) ) }}
        @if(isset($replyTo) or (isset($inputOld['reply_to'])))
            {{ Form::hidden("reply_to", (isset($inputOld['reply_to']) ? $inputOld['reply_to'] : $replyTo) ) }}
        @endif

        <div class="nf-form">
            <label class="nf-form-cell nf-cell-full @if($errors->has('content')) nf-cell-error @endif">
                @if(isset($comment->id))
                    {{ Form::textarea(
                        'content',
                        (isset($inputOld['content']) ? $inputOld['content'] : $comment->content),
                        [
                            'rows'=>'7',
                            'cols' => '50',
                            'class'=>'nf-form-input mentions',
                            'id' => 'form-share-content'
                        ]
                    ) }}
                @else
                    {{ Form::textarea(
                        'content',
                        (isset($inputOld['content']) ? $inputOld['content'] : ''),
                        [
                            'rows'=>'7',
                            'cols' => '50',
                            'class'=>'nf-form-input mentions',
                            'id' => 'form-share-content',
                            'placeholder' => trans('form.comment')
                        ]
                    ) }}
                @endif
                <span class="nf-form-label">
                    {{ trans('form.message') }}
                </span>
                {!! $errors->first('content', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
                <ul class="nf-actions emojis">
                    @include('components.emojis.emojis', ['emojiTarget' => '#form-comment-page #form-comment-content'])
                </ul>
            </label>
        </div>

        <div class="form-group clearfix modal-btns">
            <ul class="publisher">
                <li class="posting-publish-as list-inline-item">
                    <span>{{ trans('netframe.publishAs') }} :</span>
                    @if(isset($comment->id) or isset($inputOld))
                        {!! HTML::publishAs(
                            '#form-comment-page',
                            $NetframeProfiles,
                            [
                                'id'=>'author_id',
                                'type'=>'author_type',
                                'postfix'=>'cm'
                            ],
                            (isset($inputOld['author_id'])) ? ['id'=>$inputOld['author_id'], 'type'=>$inputOld['author_type']] : ['id' => $comment->author_id, 'type' => strtolower($comment->author_type), true ]
                        ) !!}
                    @else
                        {!! HTML::publishAs(
                            '#form-comment-page',
                            $NetframeProfiles,
                            ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], true, null
                        ) !!}
                    @endif
                </li>
            </ul>

            <div class="float-right">
                <button type="submit" class="button primary">
                    {{ trans('form.publish') }}
                </button>
            </div>
        </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->

<script>
$("textarea.mentions").mentionsInput({source: laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels'});
</script>