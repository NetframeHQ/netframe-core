<section class="block-widget notifications">
    <a href="{{ url()->route('notifications.results') }}" class="btn-xs btn-default float-right modal-hidden">{{ trans('netframe.viewAll') }}</a>
    <h2 class="widget-title">{{ trans('notifications.results') }}</h2>

@foreach($results as $result)
        <div class="media">
            <div class="media-left">
                <a href="{{ $result->urlUser }}">
                    {{ HTML::thumbnail($result->profilePicture, '40', '40', array('class' => 'img-fluid'), asset('/assets/img/avatar/'.$result->avatarPicture.'.jpg')) }}
                </a>
            </div>
            <div class="media-body searchResultContent">
                {{ $result->type == config('notification.type.house.accepted') || $result->type == config('notification.type.community.accepted')
                    || $result->type == config('notification.type.project.accepted')  ?
                   $result->profileInfos : HTML::link($result->urlUser, $result->userInfos)
                 }},

                    @if( $result->type == config('notification.type.friends.send'))
                        {{ $result->notifyInfos }}

                    @elseif($result->type == config('notification.type.friends.accepted'))
                        {{ $result->notifyInfos }}

                    @elseif( $result->type == config('notification.type.projectCommunity.send'))
                        {{ $result->notifyInfos }} :
                            <a href="{{ url()->route('page.project', [$result->projectCommunity_id, Str::slug($result->projectCommunity_name) ])}}">
                                {{ $result->projectCommunity_name }}
                            </a>

                    @elseif($result->type == config('notification.type.projectCommunity.accepted'))
                        {{ $result->notifyInfos }}
                        <a href="{{ url()->route('page.project', [$result->project_id, Str::slug($result->project_name) ])}}">
                            {{ $result->project_name }}
                        </a>

                    @elseif( $result->type == config('notification.type.comment.send'))
                        {{ $result->notifyInfos }} {{ $result->linkToElement }}{{ $result->linkName }}</a>
                         : {{ $result->comment }}

                    @elseif( $result->type == config('notification.type.post.share'))
                        {{ $result->notifyInfos }}
                        <div class="well">
                            <a href="{{ url()->route('post.modal', $result->idNewsFeed) }}" data-toggle="modal" data-target="#modal-ajax">
                                {{ $result->resumePost }}
                            </a>
                        </div>

                    @elseif( $result->type == config('notification.type.post.shareProfile'))
                            {{ $result->notifyInfos }}
                            <a href="{{ $result->profile->getUrl() }}">
                                {{ trans('netframe.'.$result->profile->getType()) }}
                                {{ $result->profile->getNameDisplay() }}
                            </a>

                    @elseif( $result->type == config('notification.type.post.shareMedia'))
                            {{ $result->notifyInfos }}
                            @if(!$result->media->isTypeDisplay())
                                <a href="{{ url()->route('media_download', array('id' => $result->media->id)) }}">
                                @else
                                <a class="viewMedia"
                                    data-media-name="{{ $result->media->name }}"
                                    data-media-id="{{ $result->media->id }}"
                                    data-media-type="{{ $result->media->type }}"
                                    data-media-platform="{{ $result->media->platform }}"
                                    data-media-mime-type="{{ $result->media->mime_type }}"

                                    @if('local' !== $result->media->platform)
                                        data-media-file-name="{{ $result->media->file_name }}"
                                    @endif
                                    >
                                @endif
                                    {{ $result->notifyInfos2 }}
                                </a>


                    @elseif( $result->type == config('notification.type.house.send'))
                        {{ $result->notifyInfos }} <strong><a href="{{ url()->route('page.house', [$result->house_id, Str::slug($result->house_name) ])}}">{{ $result->house_name }} </a></strong>
                        {{ $result->as }}
                            @if($result->profile == 'user')
                                {{ HTML::link($result->urlUser, $result->userInfos) }}
                            @else
                                <a href="{{ url()->route('page.'.$result->profile.'', [$result->guest_id, Str::slug($result->guest_name) ])}}">{{ $result->guest_name }} </a>
                            @endif

                    @elseif($result->type == config('notification.type.house.accepted'))
                        <a href="{{ url()->route('page.house', [$result->house_id, Str::slug($result->house_name) ])}}">{{ $result->house_name }} </a> {{ $result->notifyInfos }}

                    @elseif( $result->type == config('notification.type.community.send'))
                        {{ $result->notifyInfos }} <strong><a href="{{ url()->route('page.community', [$result->community_id, Str::slug($result->community_name) ])}}">{{ $result->community_name }} </a></strong>

                        {{ $result->as }}
                            @if($result->profile == 'user')
                                {{ HTML::link($result->urlUser, $result->userInfos) }}
                            @else
                                <a href="{{ url()->route('page.'.$result->profile.'', [$result->guest_id, Str::slug($result->guest_name) ])}}">{{ $result->guest_name }} </a>
                            @endif

                    @elseif($result->type == config('notification.type.project.accepted'))
                        <a href="{{ url()->route('page.project', [$result->project_id, Str::slug($result->project_name) ])}}">{{ $result->project_name }} </a>{{ $result->notifyInfos }}

                    @elseif( $result->type == config('notification.type.project.send'))
                        {{ $result->notifyInfos }} <strong><a href="{{ url()->route('page.project', [$result->project_id, Str::slug($result->project_name) ])}}">{{ $result->project_name }} </a></strong>

                        {{ $result->as }}
                        <a href="{{ $result->guest->getUrl() }}">{{ trans('netframe.'.$result->guest->getType()) }} {{ $result->guest->getNameDisplay() }} </a>

                    @elseif($result->type == config('notification.type.community.accepted'))
                        <a href="{{ url()->route('page.community', [$result->community_id, Str::slug($result->community_name) ])}}">{{ $result->community_name }} </a> {{ $result->notifyInfos }}

                    @elseif($result->type == config('notification.type.actions.likeProfile'))
                        {{  $result->notifyInfos }}
                        <a href="{{ $result->element->getUrl() }}">{{ $result->element->getNameDisplay() }} - {{ $result->element->getType() }}</a>

                    @elseif($result->type == config('notification.type.actions.likeContent'))
                        {{  $result->notifyInfos }}

                        @if(get_class($result->element) == 'Netframe\\Media\\Model\\Media')
                            @if(!$result->element->isTypeDisplay())
                                <a href="{{ url()->route('media_download', array('id' => $result->element->id)) }}">
                                @else
                                <a class="viewMedia"
                                    data-media-name="{{ $result->element->name }}"
                                    data-media-id="{{ $result->element->id }}"
                                    data-media-type="{{ $result->element->type }}"
                                    data-media-platform="{{ $result->element->platform }}"
                                    data-media-mime-type="{{ $result->element->mime_type }}"

                                    @if('local' !== $result->element->platform)
                                        data-media-file-name="{{ $result->element->file_name }}"
                                    @endif
                                    >
                                @endif
                                    {{ $result->notifyInfos2 }}
                                </a>
                        @elseif(get_class($result->element) == 'Comment')
                                {{ trans('notifications.comment') }} : {{ $result->element->content }}
                        @else
                            <a href="{{ url()->route('post.modal', [$result->element->posts()->first()->id]) }}" data-toggle="modal" data-target="#modal-ajax">{{ trans('notifications.likePost') }}</a>
                        @endif

                    @elseif($result->type == config('notification.type.actions.followProfile'))
                        {{  $result->notifyInfos }}
                        <a href="{{ $result->element->getUrl() }}">{{ $result->element->getNameDisplay() }} - {{ $result->element->getType() }}</a>

                    @elseif($result->type == config('notification.type.actions.clipMedia'))
                        {{  $result->notifyInfos }}

                    @elseif($result->type == config('notification.type.actions.clipProfile'))
                        {{  $result->notifyInfos }}
                        <a href="{{ $result->element->getUrl() }}">{{ $result->element->getNameDisplay() }} - {{ $result->element->getType() }}</a>

                    @elseif($result->type == config('notification.type.event.participateEvent'))
                            {{ $result->notifyInfos }} : {{ $result->event_name }}
                    @endif
            </div>
        </div>
@endforeach
</section>