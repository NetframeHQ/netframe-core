@if($modal)
    <div class="modal-header posting-system">
        <h4 class="modal-title">
            {{ trans('posting.'.class_basename($post).'.edit') }}
        </h4>

        <a class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">{{trans('form.close') }}</span>
        </a>
    </div>
    <!-- End MODAL-HEADER -->

    <div class="modal-body">
@endif

<article class="panel panel-default panel-create-post @if($modal) mg-bottom-0 @endif {{ (!isset($hideControls)) ? 'panel-focus' : '' }}">
        <div class="panel-default placeholder">
            <div class="avatar">
                {!! HTML::thumbImage(
                    auth()->guard('web')->user()->profile_media_id,
                    60,
                    60,
                    [],
                    'user_big',
                    '',
                    auth()->guard('web')->user()
                ) !!}
            </div>
            <label class="panel-post-addattachments" for="panel-post-input-photo" id="fake-attach">
                <span class="svgicon">
                    @include('macros.svg-icons.attach')
                </span>
            </label>
        </div>
        <header class="panel-heading tl-hidden-post-form {{ (isset($hideControls)) ? 'd-none' : '' }}">
            @if(!$modal)
                <div class="panel-tabs-wrapper">
                    <ul class="nf-actions">
                        <li class="nf-action">
                            <a class="nf-btn tl-close-posting" data-dismiss="modal">
                                <span aria-hidden="true" class="svgicon btn-img">
                                    @include('macros.svg-icons.plus')
                                </span>
                                <span class="btn-txt">
                                    Fermer
                                </span>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-tabs list-unstyled panel-tabs d-flex" role="tablist">
                        <li>
                            <a href="{{ url()->route('posting.default', ['post-type' => 'news']) }}" data-action="tl-post" data-type-post="news" data-context="{{ ($modal) ? 'modal' : 'inline' }}" class="@if(!isset($typePost) || $typePost == 'news') active @endif">
                                <span class="svgicon">
                                    @include('macros.svg-icons.news')
                                </span>
                                {{ trans('posting.News.new') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ url()->route('posting.default', ['post-type' => 'event']) }}" data-action="tl-post" data-type-post="event" data-context="{{ ($modal) ? 'modal' : 'inline' }}" class="@if($typePost == 'event') active @endif">
                                <span class="svgicon">
                                    @include('macros.svg-icons.event')
                                </span>
                                {{ trans('posting.TEvent.new') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ url()->route('posting.default', ['post-type' => 'offer']) }}" data-action="tl-post" data-type-post="offer" data-context="{{ ($modal) ? 'modal' : 'inline' }}" class="@if($typePost == 'offer') active @endif">
                                <span class="svgicon">
                                    @include('macros.svg-icons.offer')
                                </span>
                                {{ trans('posting.Offer.new') }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </header>

        <div class="panel-body">
            <div class="panel-post-attachments-wrapper tl-hidden-post-form {{ (isset($hideControls)) ? 'd-none' : '' }}">
                <ul class="panel-post-attachments tl-posted-medias">
                    @if(isset($mediasIds) && !empty($mediasIds))
                        @foreach(explode(',',$mediasIds) AS $mediaId)
                            <li class="file-{{ $mediaId }}" data-file-id="{{ $mediaId }}">
                                {!! HTML::thumbnail(\App\Media::find($mediaId), '', '', ['class' => ''], asset('assets/img/no-media.jpg')) !!}
                                <p></p>
                                <a class="fn-remove-media close">
                                    <span class="svgicon">
                                        @include('macros.svg-icons.close')
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    @endif
                    <li class="add">
                        @if(!session('reachInstanceQuota') && !session('reachUserQuota'))
                            {{ Form::open(array('route' => 'media_upload', 'files' => true, 'id' => 'fileupload', 'name' => 'upload')) }}
                                <label class="panel-post-addattachments">
                                    <span class="tags-icon">
                                        <span class="svgicon">
                                            @include('macros.svg-icons.attach')
                                        </span>
                                    </span>
                                    {{ Form::file('files[]', array('multiple' => true, 'accept' => '*', 'id' => 'panel-post-input-photo')) }}
                                    <span class="txt">{{ trans('posting.addMedia') }}</span>
                                </label>
                            {{ Form::close() }}
                        @else
                            <a href="{{ url()->route('media.quota.reach') }}" class="btn btn-border-default btn-sm fileinput-button" data-target="#modal-ajax" data-toggle="modal">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>{{ trans('media::messages.add_from_cam_photo') }} / {{ trans('media::messages.add_from_cam_video') }} / {{ trans('media::messages.add_document') }}</span>
                            </a>
                        @endif
            </li>
                </ul>
            </div>
        </div>

        {{ Form::open(['route'=> 'posting.default', 'id' => $form_id, 'class' => 'no-auto-submit form-posting-main', 'files' => true]) }}
        <div class="panel-body">
            {{ Form::hidden('fromCalendar', (isset($fromCalendar)) ? $fromCalendar : '0') }}

            @if(isset($post->id) && $post->id != null)
                {{ Form::hidden('id', (isset($post->id) ? $post->id : null)) }}
            @endif
            {{ Form::hidden('post_type', $post->getType()) }}

            @if($modal)
                {{ Form::hidden('modal', $modal) }}
            @endif


            <div class="panel-post-input">
                <div id="publish-as-hidden-nw">
                    {{-- preselect newsfeed author id --}}
                    {{ Form::hidden("id_foreign",
                        ((isset($post->newsfeedRef->author_id) ?
                            $post->newsfeedRef->author_id :
                            ((isset($post->default_author->author_id)) ?
                                $post->default_author->author_id
                                : auth()->guard('web')->user()->id)))
                    ) }}
                    {{ Form::hidden("type_foreign",
                        ((isset($post->newsfeedRef->author_type) ?
                            strtolower(class_basename($post->newsfeedRef->author)) :
                            ((isset($post->default_author->author_type)) ?
                                $post->default_author->author_type
                                : 'user')))
                    ) }}
                </div>

                <div id="publish-as-hidden-nwas">
                    {{-- preselect post author id --}}
                    {{ Form::hidden("id_foreign_as", (isset($post->author_id) ? $post->author_id : auth()->guard('web')->user()->id) ) }}
                    {{ Form::hidden("type_foreign_as", (isset($post->author_type) ? strtolower(class_basename($post->author)) : 'user') ) }}
                </div>

                @include('posting.content-types.'.$sub_view)

                <div class="tl-hidden-post-form panel-post-tools {{ (isset($hideControls)) ? 'd-none' : '' }}">
                    @include('components.forms.tags-posting', ['element' => $post])
                    <ul class="nf-actions">
                        @include('components.emojis.emojis', ['emojiTarget' => '#'.$form_id.' #form-post-content'])
                    </ul>
                </div>

                {{ Form::hidden('mediasIds', (isset($mediasIds) ? $mediasIds : ''), ['id' => 'postSelectedMediasId']) }}
                {{ Form::hidden('linksIds', (isset($linksIds) ? $linksIds : ''), ['id' => 'postImportedLinksId']) }}
            </div>
        </div>



        <footer class="panel-footer tl-hidden-post-form {{ (isset($hideControls)) ? 'd-none' : '' }}">
            <div class="panel-foot-actions">
                <div class="panel-foot-publishas">
                    <span class="panel-foot-publishas-label">{{ trans('netframe.publishOn') }} :</span>
                    {!! HTML::publishAs('#'.$form_id,
                        $NetframeProfiles, [
                            'id'=>'id_foreign',
                            'type'=>'type_foreign',
                            'postfix'=>'nw'
                        ],
                        true,
                        ((isset($post->newsfeedRef->author_id)) ? [
                            'id' => $post->newsfeedRef->author_id,
                            'type' => strtolower(class_basename($post->newsfeedRef->author))
                            ] :
                            ((isset($post->default_author->author_id)) ? [
                            'id' => $post->default_author->author_id,
                            'type' => $post->default_author->author_type
                            ] : null))
                    ) !!}
                </div>
                <div class="panel-foot-publishas tl-publish-as-choice {{ ( (isset($post->default_author->author_type) && $post->default_author->author_type != 'user') || (isset($post->newsfeedRef->author) && strtolower(class_basename($post->newsfeedRef->author)) != 'user') ) ? '' : 'd-none' }}">
                    <span class="panel-foot-publishas-label">{{ trans('netframe.publishAs') }} :</span>
                    {!! HTML::publishAs('#'.$form_id,
                        $NetframeProfiles, [
                            'id'=>'id_foreign_as',
                            'type'=>'type_foreign_as',
                            'postfix'=>'nwas',
                            'secondary' => true
                        ],
                        true,
                        ((isset($post->author)) ? [
                            'id' => $post->author_id,
                            'type' => strtolower(class_basename($post->author))
                        ]:
                        ((isset($post->true_author->author_id)) ? [
                        'id' => $post->true_author->author_id,
                        'type' => $post->true_author->author_type
                        ] : null))
                    ) !!}
                </div>
                <ul class="nf-actions">
                    <li class="nf-action">
                        <a href="#" class="nf-btn btn-ico btn-submenu">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.settings')
                            </span>
                        </a>
                        <div class="submenu-container submenu-right">
                            <ul class="submenu">
                                <li>
                                    <label class="nf-checkbox">
                                        {{ Form::checkbox('disable_comments', '1', (isset($post->disable_comments) && ($post->disable_comments == 1)) ? true : false, ['class' => 'panel-post-input-comments'] ) }}
                                        <span>
                                            {{ trans('posting.disableComments') }}
                                        </span>
                                    </label>
                                </li>
                                <li>
                                    <label class="nf-checkbox">
                                        {{ Form::checkbox('confidentiality', '1', (isset($post->confidentiality) && ($post->confidentiality == 0)) ? true : false, ['class' => 'panel-post-input-comments'] ) }}
                                        <span>
                                            {{ trans('posting.privatePublication') }}
                                        </span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="panel-foot-submit">
                <button type="submit" class="nf-btn btn-i btn-xxl">
                    <span class="btn-img svgicon d-none fn-spinner">
                        @include('macros.svg-icons.spinner')
                    </span>
                    <span class="btn-txt fn-submit">
                        {{ trans('form.publish') }}
                    </span>
                    <span class="btn-img svgicon">
                        @include('macros.svg-icons.arrow-right')
                    </span>
                </button>
            </div>
        </footer>

        {{ Form::close() }}
    </article>

@yield('javascripts')

@if($modal)
    </div>
    <script>

        (function () {
             var attachmentSystem = $('#modal-ajax');
             new AttachmentSystem({
                 $wrapper: attachmentSystem,
                 $fileUpload: attachmentSystem.find('#fileupload'),
                 $profileMedia: 0,
                 $postMedia: 1,
                 $mediaTemplateRender: '.tl-posted-medias',
                 $confidentiality: attachmentSystem.find('input:radio[name=confidentiality]'),
                 $profileId: {{ auth()->guard('web')->user()->id }},
                 $profileType: 'user'
             });

            $("#{{$form_id}} textarea.mentions").mentionsInput({source: laroute.route('search')+'?types[0]=users&types[1]=houses&types[2]=community&types[3]=projects&types[4]=channels&types[5]=medias', wrapper:modalPosting.$wrapper});

            modalPosting.storeUrls();
            modalPosting.storeTarget();

            {{ 'modalPosting.setup' . ucfirst($post->getType()) . '();' }}

            setTimeout(function(){
                $("#{{$form_id}} textarea.autogrow").autoGrow({
                    extraLine: true
                });
            },
            500);
        })();
    </script>
@endif
