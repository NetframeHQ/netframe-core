<div id="Media--{{$media->id}}" class="modal-foot">
    <div class="modal-author">
        {!! HTML::thumbImage(
            $author->profile_media_id,
            40,
            40,
            [],
            $author->getType(),
            'avatar',
            $author
        ) !!}
        <div class="modal-author-content">
            <div class="title">
                <a href="{{ $author->getUrl() }}">
                    @if(in_array($author->getType(), ['user', 'users']))
                        {{ $author->getNameDisplay() }}
                    @elseif(in_array($author->getType(), ['house', 'houses', 'community']))
                        {{ $author->name }}
                    @elseif(in_array($author->getType(), ['project', 'projects']))
                        {{ $author->title }}
                    @endif
                </a>

            </div>
            <div class="subtitle">
                {{ \App\Helpers\DateHelper::feedDate($media->created_at, $media->updated_at) }}
                @if ($profile->getType() != 'user')
                    , dans
                    <a href="{{ $profile->getUrl() }}">
                    @if(in_array($profile->getType(), ['house', 'houses', 'community']))
                        {{ $profile->name }}
                    @elseif(in_array($profile->getType(), ['project', 'projects']))
                        {{ $profile->title }}
                    @endif
                    </a>
                @endif
            </div>
        </div>
        {{-- <ul class="nf-actions">
            <li class="nf-action">
                <a href="#" class="nf-btn btn-submenu btn-nobg" href="#">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.menu')
                    </span>
                </a>

                <div class="submenu-container submenu-right">
                    <ul class="submenu">
                        <li>
                            <a class="nf-btn">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.plus')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('channels.dropdown.new') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul> --}}
    </div>

    {{--@php dump($author) @endphp--}}
    <!-- {{ 'projects' == $author->getType() ? $author->name : ('user' == $author->getType() ? $author->getNameDisplay() : $author->title) }} -->

    <div class="panel-footer">
        {!! HTML::likeBtn(['liked_id' => $media->id,
        'liked_type' => get_class($media),
        'liker_id' => auth()->guard('web')->user()->id,
        'liker_type' => 'user',
        'idNewsFeeds' => 0
        ],
        \App\Like::isLiked(['liked_id' => $media->id, 'liked_type' => get_class($media)]),
        $media->like,
        'foot-left',
        1) !!}
        <div class="foot-right">

            <div class="nf-post-actions nf-action-share">
                {!! HTML::shareBtnMedia($media, 'modal-share', true) !!}
            </div>

            <div class="nf-post-actions nf-action-comment">
                <a href="{{ url()->to('netframe/form-comment-media', [$media->id]) }}" class="modal-comments nf-btn" data-toggle="modal" data-target="#modal-ajax-comment2">
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.talk')
                    </span>
                    <span class="btn-digit btn-txt">
                        {{ $media->comments()->count() }}
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.comments') }}
                    </span>
                </a>
            </div>
        </div>
</div>

<div class="modal-comments-wrapper" id="modal-comment-media-{{$media->id}}">
    @if($comments != null)
        @if($comments != null && $comments->count() > 0 && !$removeMoreComments)
            <a id="link-more-comments-media-{{$media->id}}" class="fn-modal-more-comments-{{$media->id}}" data-target="#modal-comment-media-{{$media->id}}" data-media-id="{{$media->id}}">
                {{ trans('page.allComments') }}
            </a>
        @endif
        <div class="panel-comments-wrapper">

            <div class="mycomment">
                {!! HTML::thumbImage(
                    auth()->guard('web')->user()->profile_media_id,
                    40,
                    40,
                    [],
                    auth()->guard('web')->user()->getType(),
                    'av',
                    auth()->guard('web')->user()
                ) !!}

                <div class="mycomment-content">
                    <form class="fn-comment-form" id="form-comment-media-{{$media->id}}">
                        @php
                        $textId = uniqid();
                        @endphp
                        <textarea name="content" id="form-comment-textarea-{{$textId}}" class="comment-content mentions autogrow" placeholder="{{trans('form.writeHere')}}"></textarea>
                        {{ Form::hidden("post_id", $media->id) }}
                        {{ Form::hidden("post_type", get_class($media)) }}
                        <div id="publish-as-hidden-cm">
                            {{ Form::hidden("author_id",  auth()->guard('web')->user()->id) }}
                            {{ Form::hidden("author_type", 'user') }}
                        </div>
                        <div class="mycomment-actions">
                            <ul class="nf-actions">
                                @include('components.emojis.emojis', ['emojiTarget' => '#form-comment-textarea-'.$textId])
                            </ul>
                            <ul class="publisher">
                                <li class="posting-publish-as list-inline-item">
                                    <span>{{ trans('netframe.publishAs') }} :</span>
                                    @if(isset($comment->id) or isset($inputOld))
                                        {!! HTML::publishAs('#form-comment-media-'.$media->id, $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], (isset($inputOld['author_id'])) ? ['id'=>$inputOld['author_id'], 'type'=>$inputOld['author_type']] : ['id' => $comment->author_id, 'type' => strtolower($comment->author_type), true ]) !!}
                                    @else
                                        {!! HTML::publishAs('#form-comment-media-'.$media->id, $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], true, null) !!}
                                    @endif
                                </li>
                            </ul>
                            <button type="submit" class="nf-btn btn-i">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.send')
                            </span>
                            <span class="btn-txt">
                                {{ trans('form.publish') }}
                            </span>
                            </button>
                        </div>
                        <div class="mycomment-fx"></div>
                    </form>
                </div>
            </div>
            <ul class="list-unstyled comments-list">
                @foreach($comments as $comment)
                    @include('page.comment')
                @endforeach
            </ul>
        </div>
    @endif
</div>
{{--
<div class="comments-write text-center">
    <a href="{{ url()->to('netframe/form-comment-media', [$media->id]) }}" data-toggle="modal" data-target="#modal-ajax-comment2">
        {{ trans('netframe.comment') }}
    </a>
</div>
--}}