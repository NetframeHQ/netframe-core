@if($comment->post_type == 'App\Media')
    <li class="comment-{{$comment->id}}" id="comment-{{ $comment->id }}-Media-{{ $comment->post_id }}"  data-idcomment="{{ $comment->id }}" data-date="{{ $comment->created_at }}" @if($comment->level>1) style="margin-left: {{50*($comment->level-1)}}px" @endif>
@else
    <li class="comment-{{$comment->id}}" id="comment-{{ $comment->id }}-{{ class_basename($comment->post) }}-{{ $comment->post_id }}"  data-idcomment="{{ $comment->id }}" data-date="{{ $comment->created_at }}" @if($comment->level>1) style="margin-left: {{50*($comment->level-1)}}px" @endif>
@endif
        @if($comment->level>1)
            <div class="comment-fx"></div>
        @endif

        <div class="comment-container">
            <div class="comment-avatar">
                <a href="{{ $comment->author->getUrl() }}">
                    {!! HTML::thumbImage(
                        $comment->author->profile_media_id,
                        30,
                        30,
                        [],
                        $comment->author->getType(),
                        null,
                        $comment->author
                    ) !!}
                </a>
            </div>
            <div class="comment-content">
                <a href="{{ $comment->author->getUrl() }}" class="comment-username usercolor4">{{ $comment->author->getNameDisplay() }}</a>
                <p class="comment-time">
                    {{ \App\Helpers\DateHelper::feedDate($comment->created_at, $comment->updated_at) }}
                </p>
                <p class="comment-txt">
                    {!! \App\Helpers\StringHelper::collapsePostText($comment->content) !!}
                </p>
            </div>
        </div>

        <div class="comment-menu">
            @if(class_basename($post) != 'NetframeAction'
                && ( ( App\Http\Controllers\BaseController::hasRights($comment) && App\Http\Controllers\BaseController::hasRights($comment) < 3 )
                || ( App\Http\Controllers\BaseController::hasRightsProfile($comment->post->author) && App\Http\Controllers\BaseController::hasRightsProfile($comment->post->author) < 3 )
                || ( class_basename($post) == 'Media' && App\Http\Controllers\BaseController::hasRightsProfile($comment->post->author->first()) && App\Http\Controllers\BaseController::hasRightsProfile($comment->post->author->first()) < 3 ) )
                || ($comment->post->newsfeedRef != null && App\Http\Controllers\BaseController::hasRights($comment->post->newsfeedRef) && App\Http\Controllers\BaseController::hasRights($comment->post->newsfeedRef) < 4)
                )
                <a href="#" class="nf-btn btn-submenu btn-ico btn-nobg">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.menu')
                    </span>
                </a>
                <div class="submenu-container submenu-right">
                    <ul class="submenu">
                        @if(App\Http\Controllers\BaseController::hasRights($comment) && App\Http\Controllers\BaseController::hasRights($comment) < 3)
                            <li>
                                <a class="nf-btn" href="{{ url()->to('netframe/edit-comment', [$comment->id]) }}" data-toggle="modal" data-target="#modal-ajax-comment2">
                                    <span class="btn-txt">
                                        {{ trans('netframe.edit') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a class="nf-btn fn-confirm-delete fn-ajax-delete" href="{{ url()->route('netframe.delete.comment', ['idComment' => $comment->id ]) }}"  data-txtconfirm="{{ trans('netframe.confirmDelComment') }}">
                                <span class="btn-txt">
                                    {{ trans('netframe.delete') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="comment-actions">
                {!! HTML::likeBtnComment(
                    [
                        'liked_id'=>$comment->id,
                        'liked_type' => 'App\\Comment',
                        'liker_id' => auth()->guard('web')->user()->id,
                        'liker_type' => 'user'
                    ],
                    \App\Like::isLiked(['liked_id' => $comment->id, 'liked_type' => 'App\\Comment']),
                    $comment->like,
                    'comments-like'
                ) !!}
            @if($comment->post_type != 'App\\Media')
                <div class="comment-replies">
                    @if($comment->replies()->count() > 0)
                        <a class="nf-btn btn-nobg fn-all-replies" data-post-id="{{ $post->id }}" data-comment-id="{{$comment->id}}">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.talk')
                            </span>
                            <span class="btn-txt">
                                {{ $comment->replies()->count() }}
                            </span>
                        </a>
                    @endif
                    @if($comment->level < 4)
                        @if($comment->post_type == 'App\\Media')
                            <a href="{{route('netframe.form-comment',['typeElement' => 'media', 'idElement' => $comment->post->id, 'replyTo' => $comment->id])}}" class="nf-btn btn-nobg" data-toggle="modal" data-target="#modal-ajax-comment">
                        @else
                            <a href="{{route('netframe.form-comment',['typeElement' => 'newsfeed', 'idElement' => $comment->post->newsfeedRef, 'replyTo' => $comment->id])}}" class="nf-btn btn-nobg" data-toggle="modal" data-target="#modal-ajax-comment">
                        @endif
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.comment-reply')
                            </span>
                            <span class="btn-txt">
                                {{trans('netframe.reply')}}
                            </span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </li>
