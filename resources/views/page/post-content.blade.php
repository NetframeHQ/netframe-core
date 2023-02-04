<!-- POST RESPONSE MOD -->
@if($post->post != null)
  @php
    $object = $post->post;
    //Post viewed by current user
    // dump(get_class($post));
    $post->view();
  @endphp

    <article id="{{ class_basename($post->post) }}-{{ class_basename($post->author) }}-{{ $post->post_id }}" class="panel panel-default topic post-{{ $post->author_type }}-{{ $post->author_id }} @if(isset($pintop)) panel-active @endif" data-time="{{ (isset($post->created_at2)) ? $post->created_at2 : $post->created_at }}" data-newsfeed-id="{{ $post->id }}">
    <header class="panel-heading">
      <div class="panel-infos">
        @if($post->post_type != 'App\\NetframeAction')
          <div class="panel-avatar">
            @if($post->true_author != $post->author && class_basename($post->author) != 'Channel')
                {!! HTML::thumbImage(
                    $post->author->profile_media_id,
                    30,
                    30,
                    [],
                    $post->author->getType(),
                    '',
                    $post->author
                ) !!}
            @else
                {!! HTML::thumbImage(
                    $post->true_author->profile_media_id,
                    30,
                    30,
                    [],
                    $post->true_author->getType(),
                    '',
                    $post->true_author
                ) !!}
            @endif
          </div>

          <p class="panel-description">
            @if($post->true_author != $post->author && class_basename($post->author) != 'Channel')
               <a href="{{$post->author->getUrl()}}">
                {{ $post->author->getNameDisplay() }}
               </a>
             @else
               <a href="{{$post->post->author->getUrl()}}">
                {{ $post->true_author->getNameDisplay() }}
               </a>
             @endif

             @if($post->post_type == 'App\\Share')
              @include("page.type-content.share-header", ['post' => $post->post])
             @endif
          </p>
          <p class="panel-time" title="{{ $post->post->created_at }}">
            <a href="{{ $post->post->getUrl() }}">{{ \App\Helpers\DateHelper::feedDate($post->post->created_at, $post->post->updated_at) }}</a>
            @if($post->true_author->id.'--'.class_basename($post->true_author) != $post->author->id.'--'.class_basename($post->author) && class_basename($post->author) != 'Channel')
            , {{ trans('page.by') }} <a href="{{ $post->true_author->getUrl() }}"> {{ $post->true_author->getNameDisplay() }}</a>
            @endif
          </p>
        @else
          <div class="panel-avatar">
            @if($post->true_author != $post->author && class_basename($post->author) != 'Channel')
                {!! HTML::thumbImage(
                    $post->author->profile_media_id,
                    30,
                    30,
                    [],
                    $post->author->getType(),
                    '',
                    $post->author
                ) !!}
            @else
                {!! HTML::thumbImage(
                    $post->true_author->profile_media_id,
                    30,
                    30,
                    [],
                    $post->true_author->getType(),
                    '',
                    $post->true_author
                ) !!}
            @endif
          </div>
          <p class="panel-description">
             <a href="{{$post->author->getUrl()}}">
              {{ $post->author->getNameDisplay() }}
             </a>
             @include("page.type-content.netframe-action-header", ['Taction'=>$post->post])
          </p>
          <p class="panel-time" title="{{ $post->post->updated_at }}">
            <a href="{{$post->post->getUrl()}}">
              {{ \App\Helpers\DateHelper::feedDate($post->post->updated_at, $post->post->updated_at) }}
            </a>
          </p>
        @endif
      </div>

      <div class="panel-actions nf-actions">
        <!-- PIN BUTTON -->
        @if(App\Http\Controllers\BaseController::hasRights($post) && App\Http\Controllers\BaseController::hasRights($post) <= 3 && class_basename($post->author) != "User")
          <li class="nf-action">
            {!! HTML::pinTop(['id_post'=>$post->post_id, 'type_post'=>$post->post_type, 'id_foreign'=>$post->author_id, 'type_foreign'=>$post->author_type, 'pinned' => $post->pintop]) !!}
          </li>
        @endif

        <!-- ••• MENU -->
        @if($post->post_type != 'App\NetframeAction'
          && (
            (App\Http\Controllers\BaseController::hasRights($post->post) && App\Http\Controllers\BaseController::hasRights($post->post) < 4)
            || (App\Http\Controllers\BaseController::hasRights($post) && App\Http\Controllers\BaseController::hasRights($post) < 4)
          ))
          <li class="nf-action">
            <a href="#" class="nf-btn btn-ico btn-nobg btn-submenu">
              <span class="svgicon btn-img">
                @include('macros.svg-icons.menu')
              </span>
            </a>
            <div class="submenu-container submenu-right">
              <ul class="submenu">
                <li>
                  <a href="#" class="nf-btn fn-stop-follow" data-profile-type="{{ $post->author_type }}" data-profile-id="{{ $post->author_id }}">
                    <span class="btn-txt">
                      {{ trans('netframe.stopFollow') }}
                    </span>
                  </a>
                </li>
                @if($post->post_type != 'NetframeAction')
                  <li>
                    <a class="nf-btn" href="{{ action('NetframeController@getReportAbuse', ['authorId' => $post->author_id, 'postId' => $post->post_id, 'postType' => class_basename($post->post)]) }}" class="link-netframe btn-sm pull-right" data-toggle="modal" data-target="#modal-ajax">
                      <span class="btn-txt">
                        {{ trans('netframe.reportAbus') }}
                      </span>
                    </a>
                  </li>
                @endif
                <li class="sep"></li>
                @if($post->post_type == 'App\TEvent')
                  @if(App\Http\Controllers\BaseController::hasRights($post) && App\Http\Controllers\BaseController::hasRights($post) < 4)
                    <li>
                      <a class="nf-btn" href="{{ url()->route('posting.default', ['post_type' => 'event', 'post_id' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                        <span class="btn-txt">
                          {{ trans('netframe.edit') }}
                        </span>
                      </a>
                    </li>
                    <li>
                      <a class="nf-btn" href="{{ url()->route('calendar.synchronize', ['event_id' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                        <span class="btn-txt">
                          {{ trans('netframe.exportTo') }}
                        </span>
                      </a>
                    </li>
                  @endif
                  <li class="sep"></li>
                  <li>
                    <a class="fn-confirm-delete fn-ajax-delete nf-btn" href="{{ url()->to('netframe/delete-publish', [$post->id]) }}" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                      <span class="btn-txt">
                        {{ trans('netframe.delete') }}
                      </span>
                    </a>
                  </li>

                @elseif($post->post_type == 'App\News')
                  @if(App\Http\Controllers\BaseController::hasRights($post) && App\Http\Controllers\BaseController::hasRights($post) < 4)
                    <li>
                      <a class="nf-btn nf-fn-update-post" href="{{ url()->route('posting.default', ['post_type' => 'news', 'post_id' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                        <span class="btn-txt">
                          {{ trans('netframe.edit') }}
                        </span>
                      </a>
                    </li>
                  @endif
                  <li class="sep"></li>
                  <li>
                    <a href="{{ url()->to('netframe/delete-publish', [$post->id]) }}" class="nf-btn fn-confirm-delete fn-ajax-delete" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                      <span class="btn-txt">
                        {{ trans('netframe.delete') }}
                      </span>
                    </a>
                  </li>

                @elseif($post->post_type == 'App\Share')
                  @if(App\Http\Controllers\BaseController::hasRights($post) && App\Http\Controllers\BaseController::hasRights($post) < 4)
                    <li>
                      @if(class_basename($post->post->post) == 'Media')
                        <a class="nf-btn" href="{{ url()->route('form.share.media', ['mediaId' => $post->post->media_id, 'id' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                          <span class="btn-txt">
                            {{ trans('netframe.edit') }}
                          </span>
                        </a>
                      @elseif( in_array(class_basename($post->post->post), config('netframe.shareProfilesTypes')) )
                        <a class="nf-btn" href="{{ url()->route('form.share.profile', ['typeProfile' => class_basename($post->post->post), 'idProfile' => $post->post->post_id, 'id' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                          <span class="btn-txt">
                            {{ trans('netframe.edit') }}
                          </span>
                        </a>
                      @else
                        <a class="nf-btn" href="{{ url()->route('form.share', ['idNewsfeed' => $post->id, 'idShare' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                          <span class="btn-txt">
                            {{ trans('netframe.edit') }}
                          </span>
                        </a>
                      @endif
                    </li>
                  @endif
                  <li class="sep"></li>
                  <li>
                    <a href="{{ url()->to('netframe/delete-publish', [$post->id]) }}" class="nf-btn fn-confirm-delete fn-ajax-delete" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                      <span class="btn-txt">
                        {{ trans('netframe.delete') }}
                      </span>
                    </a>
                  </li>

                @elseif($post->post_type == 'App\Offer')
                  @if(App\Http\Controllers\BaseController::hasRights($post) && App\Http\Controllers\BaseController::hasRights($post) < 4)
                    <li>
                      <a class="nf-btn" href="{{ url()->route('posting.default', ['post_type' => 'offer', 'post_id' => $post->post_id]) }}" data-toggle="modal" data-target="#modal-ajax">
                        <span class="btn-txt">
                          {{ trans('netframe.edit') }}
                        </span>
                      </a>
                    </li>
                  @endif
                  <li class="sep"></li>
                  <li>
                    <a href="{{ url()->to('netframe/delete-publish', [$post->id]) }}" class="nf-btn fn-confirm-delete fn-ajax-delete" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                      <span class="btn-txt">
                        {{ trans('netframe.delete') }}
                      </span>
                    </a>
                  </li>
                @endif

              </ul>
            </div>
          </li>
        @else
          <li class="nf-action">
            <a href="#" class="nf-btn btn-ico btn-nobg btn-submenu">
              <span class="svgicon btn-img">
                @include('macros.svg-icons.menu')
              </span>
            </a>
            <div class="submenu-container submenu-right">
              <ul class="submenu">
                <li>
                  <a href="#" class="nf-btn fn-stop-follow" data-profile-type="{{ $post->author_type }}" data-profile-id="{{ $post->author_id }}">
                    <span class="btn-txt">
                      {{ trans('netframe.stopFollow') }}
                    </span>
                  </a>
                </li>
                @if($post->post_type != 'NetframeAction')
                  <li>
                    <a href="{{ action('NetframeController@getReportAbuse', ['authorId' => $post->author_id, 'postId' => $post->post_id, 'postType' => class_basename($post->post)]) }}" class="nf-btn link-netframe btn-sm pull-right" data-toggle="modal" data-target="#modal-ajax">
                      <span class="btn-txt">
                        {{ trans('netframe.reportAbus') }}
                      </span>
                    </a>
                  </li>
                @endif
              </ul>
            </div>
          </li>
        @endif
      </div>
    </header>
    <!-- END PANEL HEADING -->

    <div class="panel-body">
      @if($post->post_type != "App\NetframeAction" && $post->post_type != "App\TaskTable")
        @if($post->post->medias!=null && $post->post->medias->count() > 0)
          @if($post->post->onlyImages())
            @include("page.type-content.medias.multi-medias")
          @else
            @include("page.type-content.medias.medias")
          @endif
        @endif
      @endif

      @if($post->post_type === "App\News")
        @if($post->post->content != null)
          <div class="panel-post">
            @include("page.type-content.news")
          </div>
        @endif
        @include("page.type-content.links", ['post' => $post->post])

      @elseif($post->post_type === "App\TEvent")
        <div class="panel-event">
          @include("page.type-content.event")
        </div>
        @include("page.type-content.links", ['post' => $post->post])

      @elseif($post->post_type === "App\Offer")
        <div class="panel-event">
          @include("page.type-content.offer" )
        </div>
        @include("page.type-content.links", ['post' => $post->post])

      @elseif($post->post_type === "App\Share")
        @include("page.type-content.share", ['post' => $post->post])

      @elseif($post->post_type === "App\Playlist")
        {{ nl2br($post->post->content) }}
        @include("page.type-content.playlist")

      @elseif($post->post_type === "App\NetframeAction")
        @include("page.type-content.netframe-actions", ['Taction'=>$post->post])


      @elseif($post->post_type === "App\TaskTable")
        <a href="{{ $post->post->getUrl() }}" class="panel-document">
          <div class="panel-document-head">
            <div class="panel-document-icon">
              <span class="svgicon">
                  @include('macros.svg-icons.tasks_big')
              </span>
            </div>
            <div class="panel-document-info">
                <h3 class="panel-document-title">
                  {{ $post->post->name }}
                </h3>
                <p class="panel-document-subtitle">
                  {{ trans('netframe.leftMenu.task') }}
                </p>
            </div>
          </div>
        </a>
      @endif

      @if(in_array(class_basename($post->post), config('netframe.model_taggables')) && $post->post->tags()->count() > 0)
        <div class="panel-tags">
          <span class="tags-icon">
            <span class="svgicon">
              @include('macros.svg-icons.tags')
            </span>
          </span>
          @include('tags.element-display', ['tags' => $post->post->tags])
        </div>
      @endif
    </div>
    <!-- END PANEL BODY -->

    <footer class="panel-footer">
      <!-- LIKE BUTTON -->
      {!! HTML::likeBtn(['liked_id' => $post->post_id,
        'liked_type' => get_class($post->post),
        'liker_id' => auth()->guard('web')->user()->id,
        'liker_type' => 'user',
        'idNewsFeeds' => $post->id
        ],
        \App\Like::isLiked(['liked_id' => $post->post_id, 'liked_type' => get_class($post->post)]),
        $post->like,
        'foot-left')
      !!}

      <div class="foot-right">
        <!-- COMMENT BUTTON -->
        @if($post->post->disable_comments == 0)
          @php
            $postComments = $post->post->comments()->limit(5)->get();
            $commentsCount = $post->post->comments()->count();
          @endphp
          <div class="nf-post-actions nf-action-comment">
            <a class="nf-btn fn-all-comments" data-post-id="{{ $post->id }}">
              <span class="btn-img svgicon">
                @include('macros.svg-icons.talk')
              </span>
              <span class="btn-txt btn-digit">

                {{-- $post->post->comments()->count() --}}

                {{$commentsCount}}

              </span>
              <span class="btn-txt">
                {{ trans('netframe.comments') }}
              </span>
            </a>
            @if($commentsCount>0)
            <div class="nf-tooltip">
              <div class="tooltip-list">
                <ul>
                    @php
                      $ids = []
                    @endphp
                    @foreach($postComments as $comment)
                      @if(!in_array($comment->author->id, $ids))
                        <li><a href="{{ $comment->author->getUrl() }}">{{$comment->author->getNameDisplay()}}</a></li>
                        @php
                          $ids[] = $comment->author->id
                        @endphp
                      @endif
                    @endforeach
                    @if($commentsCount>5)
                    <li><a href="#">...</a></li>
                    @endif
                </ul>
              </div>
              @if($commentsCount <= 1)
                <p class="tooltip-txt">{{trans('netframe.commentedThisPostSingular')}}</p>
              @else
                <p class="tooltip-txt">{{trans('netframe.commentedThisPost')}}</p>
              @endif
            </div>
            @endif
          </div>
        @endif

        <!-- SHARE BUTTON -->
        @if($post->post_type != "App\NetframeAction" && $post->confidentiality == 1)
            <div class="nf-post-actions nf-action-share">
                @if($post->post_type === "App\\Share" && $post->post->news_feed_id !== null)
                    @php
                        $sharePost = $post->post->post->posts()->first()
                    @endphp
                    {!! HTML::shareBtn($sharePost) !!}
                @elseif($post->post->post_type == 'App\\Media')
                    {!! HTML::shareBtnMedia($post->post->post, 'float-left') !!}
                @elseif( in_array(class_basename($post->post->post), config('netframe.shareProfilesTypes')) )
                    {!! HTML::shareBtnProfile($post->post->post, true) !!}
                @elseif($post->post_type == 'Playlist')
                    {!! HTML::shareBtn($post) !!}
                @else
                    {!! HTML::shareBtn($post) !!}
                @endif

                @php
                    if($post->post_type !== "App\\Share"){
                        $postShares = $post->post->shares()->limit(5)->get();
                        $sharesCount = $post->post->shares()->count();
                    }elseif(isset($sharePost)){
                        $postShares = $sharePost->post->shares()->limit(5)->get();
                        $sharesCount = $sharePost->post->shares()->count();
                    } else {
                        $postShares = [];
                        $sharesCount = 0;
                    }
                @endphp
                @if($sharesCount>0)
                    <div class="nf-tooltip">
                        <div class="tooltip-list">
                            <ul>
                                @php
                                    $ids = []
                                @endphp
                                @foreach($postShares as $comment)
                                    @if(!in_array($comment->author->id, $ids))
                                        <li><a href="{{ $comment->author->getUrl() }}">{{$comment->author->getNameDisplay()}}</a></li>
                                        @php
                                            $ids[] = $comment->author->id
                                        @endphp
                                    @endif
                                @endforeach
                                @if($sharesCount>5)
                                    <li><a href="#">...</a></li>
                                @endif
                            </ul>
                        </div>
                        @if($sharesCount <= 1)
                            <p class="tooltip-txt">{{trans('netframe.sharedThisPostSingular')}}</p>
                        @else
                            <p class="tooltip-txt">{{trans('netframe.sharedThisPost')}}</p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <!-- VIEW BUTTON -->
        @php
            $postViews = $post->views()->limit(5)->get();
            $viewsCount = $post->views()->count();
        @endphp
        @if($viewsCount > 0)
          <div class="nf-post-actions nf-action-view">
            <a href="{{ route('post.viewers', ['elementType'=>str_replace('App\\', '', get_class($post)),'elementId'=>$post->id]) }}" class="nf-btn" data-toggle="modal" data-target="#modal-ajax-thin">
              <span class="btn-img svgicon">
                @include('macros.svg-icons.view')
              </span>
              <span class="btn-txt btn-digit">{{ $viewsCount }}</span>
              <span class="btn-txt">{{ trans_choice('netframe.views', $viewsCount) }}</span>
            </a>

            @if($viewsCount>0)
            <div class="nf-tooltip">
              <div class="tooltip-list">
                <ul>
                   @foreach($postViews as $view)
                    <li><a href="{{ $view->user->getUrl() }}">{{$view->user->getNameDisplay()}}</a></li>
                    @endforeach
                    @if($viewsCount>5)
                    <li><a href="#">...</a></li>
                    @endif
                </ul>
              </div>
              @if($viewsCount <= 1)
                <p class="tooltip-txt">{{trans('netframe.viewedThisPostSingular')}}</p>
              @else
                <p class="tooltip-txt">{{trans('netframe.viewedThisPost')}}</p>
              @endif
            </div>
            @endif
          </div>
        @endif
      </div>
    </footer>


    <!-- END PANEL FOOTER -->
    <!-- COMMENT MOD -->
    <div class="panel-comments-wrapper @if($post->post->disable_comments == 1) d-none @endif">

      <!-- MY COMMENT -->
      <div class="mycomment">
        <span class="av">
        @if(auth()->guard('web')->user()->profileImage != null)
          {!! HTML::thumbnail(
            auth()->guard('web')->user()->profileImage,
            60,
            60,
            [],
            asset('assets/img/avatar/user.jpg'),
            null,
            'user',
        ) !!}
        @else
          {{--
          <span class="svgicon">
            @include('macros.svg-icons.user')
          </span>
          --}}
          {!! HTML::userAvatar(auth()->guard('web')->user(), 30) !!}
        @endif
        </span>
        <div class="mycomment-content">
        <form class="fn-comment-form" id="form-comment-post-{{$post->id}}">
           @php
           $textId = uniqid();
           @endphp
          <textarea name="content" id="form-comment-textarea-{{$textId}}" class="comment-content mentions autogrow" placeholder="{{trans('form.writeHere')}}"></textarea>
          <div class="mycomment-actions">
            <ul class="nf-actions">
              @include('components.emojis.emojis', ['emojiTarget' => '#form-comment-textarea-'.$textId, 'fromAjax' => false])
            </ul>
            {{ Form::hidden("post_id", $post->post_id) }}
            <input name="post_type" type="hidden" value="{{ get_class($post->post) }}">
            <div id="publish-as-hidden-cm">
                {{ Form::hidden("author_id",  auth()->guard('web')->user()->id) }}
                {{ Form::hidden("author_type", 'user') }}
            </div>
            <ul class="publisher">
              <li class="posting-publish-as list-inline-item">
                <span>{{ trans('netframe.publishAs') }} :</span>
                @if(isset($comment->id) or isset($inputOld))
                    {!! HTML::publishAs('#form-comment-post-'.$post->id, $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], (isset($inputOld['author_id'])) ? ['id'=>$inputOld['author_id'], 'type'=>$inputOld['author_type']] : ['id' => $comment->author_id, 'type' => strtolower($comment->author_type), true ]) !!}
                @else
                    {!! HTML::publishAs('#form-comment-post-'.$post->id, $NetframeProfiles, ['id'=>'author_id', 'type'=>'author_type', 'postfix'=>'cm'], true, null) !!}
                @endif
              </li>
            </ul>

            <button type="submit" class="nf-btn btn-primary fn-netframe-comment">
              <span class="btn-txt">
                {{ trans('form.publish') }}
              </span>
              <span class="btn-img svgicon">
                @include('macros.svg-icons.send')
              </span>
            </button>
          </div>
          <div class="mycomment-fx"></div>
        </form>
        </div>
      </div>

      <!-- COMMENTS LIST -->
      <ul class="list-unstyled comments-list">
        @if($unitPost)
          @foreach($post->post->comments AS $comment)
            @include('page.comment')
          @endforeach
        @else
          @if($post->post->lastComments != null)
            @foreach($post->post->lastComments AS $comment)
              @include('page.comment')
            @endforeach
          @endif
        @endif
      </ul>
      <!-- MORE COMMENTS VIEW -->
      @if($post->post->comments->count() > config('netframe')['number_comment'] && !$unitPost)
        <div class="post-moar">
          <a class="nf-btn fn-all-comments" data-post-id="{{ $post->id }}">
            <span class="btn-txt">
              {{ trans('netframe.moreComments') }}
            </span>
          </a>
        </div>
      @endif
    </div>
  </article>
{{--@endif--}}
@endif
