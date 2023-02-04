<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('netframe.yourComment') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body modal-share">

    {{ Form::open(['url'=> '/netframe/comment-profile', 'id' => 'form-comment-profile', 'files' => true]) }}
    <div id="publish-as-hidden-cm">
        @if(isset($comment->id) or isset($inputOld['comment_id']))
            {{ Form::hidden("comment_id", (isset($inputOld['comment_id']) ? $inputOld['comment_id'] : $comment->id) ) }}
            {{ Form::hidden("author_id", (isset($inputOld['author_id']) ? $inputOld['author_id'] : $comment->author_id) ) }}
            {{ Form::hidden("author_type", (isset($inputOld['author_type']) ? $inputOld['author_type'] : strtolower($comment->author_type)) ) }}
        @else
            {{ Form::hidden("author_id", (isset($inputOld['author_id']) ? $inputOld['author_id'] : auth()->guard('web')->user()->id) ) }}
            {{ Form::hidden("author_type", (isset($inputOld['author_type']) ? $inputOld['author_type'] : 'user') ) }}
        @endif
    </div>

    {{ Form::hidden("profile_id", (isset($inputOld['profile_id']) ? $inputOld['profile_id'] : $profileCommented->id) ) }}
    {{ Form::hidden("profile_type", (isset($inputOld['profile_type']) ? $inputOld['profile_type'] : $profileCommented->getType()) ) }}

    <div class="form-group">
        {{ Form::label('content', trans('form.message')) }}
        <br />
        @if(isset($comment->id))
            {{ Form::textarea( 'content', ((isset($inputOld['content'])) ? $inputOld['content'] : $comment->content), ['rows'=>'7', 'class'=>'form-control mentions '.(($errors->has('content')) ? 'is-invalid' : ''), 'id' => 'form-comment-content'] ) }}
        @else
             {{ Form::textarea( 'content', ((isset($inputOld['content'])) ? $inputOld['content'] : null), ['rows'=>'7', 'class'=>'form-control mentions '.(($errors->has('content')) ? 'is-invalid' : ''), 'id' => 'form-comment-content'] ) }}
        @endif
        {{ $errors->first('content', '<p class="invalid-feedback">:message</p>') }}
    </div>

    <div>
        @include('components.emojis.emojis', ['emojiTarget' => '#form-comment-profile #form-comment-content'])
    </div>

    <div class="form-group clearfix modal-btns">
        <ul class="publisher">
            <li class="posting-publish-as list-inline-item">
                <span>{{ trans('netframe.publishAs') }} :</span>
                @if(isset($comment->id) or isset($inputOld))
                    {{ HTML::publishAs('#form-comment-profile', $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], true, (isset($inputOld['author_id'])) ? ['id'=>$inputOld['author_id'], 'type'=>$inputOld['author_type']] : ['id' => $comment->author_id, 'type' => strtolower($comment->author_type) ]) }}
                @else
                    {{ HTML::publishAs('#form-comment-profile', $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], true, null) }}
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

