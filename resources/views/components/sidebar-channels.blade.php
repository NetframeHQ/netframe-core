@if($channel->personnal == 0)
    <div class="content-sidebar-action">
        {!! HTML::joinProfileBtn($channel->id, $channel->getType(), auth()->guard('web')->user()->id, $joined, $channel->confidentiality, $channel->nbUsers()) !!}
    </div>
@endif

<div class="content-sidebar-action bloc-access-livechat">
    <a href="{{ url()->route('channels.livechat', ['channelId' => $channel->id]) }}" class="button primary counter button-visio" target="_blank">
        <span class="default">
            visio
        </span>
        <span class="content-link">
            <span class="svgicon">
                @include('macros.svg-icons.visio')
            </span>
            {{ trans('channels.startLive') }}
        </span>
        {{--
        <span class="num" data-members="{{ $channel->live_members }}">{{ $channel->live_members }}</span>
        --}}
    </a>
</div>

<div class="content-sidebar-action">
    @include('visio.create-link', ['channel' => $channel])
</div>

@if($channel->description != '' || $channel->tags()->count() > 0)
    <div class="content-sidebar-block">

        {{-- 
        <div class="content-sidebar-line content-sidebar-titline">
            <span class="svgicon icon-creator">
                @include('macros.svg-icons.creator')
            </span>
            <h4 class="content-sidebar-title" title="{{ trans('page.createdBy') }}">{{ trans('page.createdBy') }}</h4>
            <p>
                <strong>
                    <a href="{{ $channel->profile->getUrl() }}"> {{ $channel->profile->getNameDisplay() }}</a>
                </strong>
            </p>
        </div> 
        --}}

        @if($channel->description != '')
            <div class="content-sidebar-line">
                <span class="svgicon icon-infos">
                    @include('macros.svg-icons.infos')
                </span>
                <h4 class="content-sidebar-title" title="{{ trans('page.description') }}">{{ trans('page.description') }}</h4>
                <p>{!! \App\Helpers\StringHelper::collapsePostText($channel->description, 1000) !!}</p>
            </div>
        @endif

        @if($channel->tags()->count() > 0)
            <div class="content-sidebar-line">
                <span class="svgicon icon-tags">
                    @include('macros.svg-icons.tags')
                </span>
                <h4 class="content-sidebar-title" title="{{ trans('tags.tags') }}">{{ trans('tags.tags') }}</h4>
                @include('tags.element-display', ['tags' => $channel->tags])
            </div>
        @endif
    </div>
@endif

@if($channel->medias->count() > 0)
    <div class="content-sidebar-block">
        <a href="{{ url()->route('medias_explorer', ['profileType' => 'channel', 'profileId' => $channel->id]) }}" class="nf-invisiblink"></a>
        <div class="content-sidebar-line content-sidebar-titline">
            <span class="svgicon icon-docs">
                @include('macros.svg-icons.doc')
            </span>
            <h4 class="content-sidebar-title" title="{{ trans('widgets.documents') }}">{{ trans('widgets.documents') }}</h4>
        </div>
        <ul class="netframe-list">
            @foreach($channel->lastMedias() as $media)
                <li>
                    @if (!$media->isTypeDisplay())
                        <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="item">
                    @else
                        <a href="" class="viewMedia item"
                            data-media-name="{{ $media->name }}"
                            data-media-id="{{ $media->id }}"
                            data-media-type="{{ $media->type }}"
                            data-media-platform="{{ $media->platform }}"
                            data-media-mime-type="{{ $media->mime_type }}"

                            @if ($media->platform !== 'local')
                                data-media-file-name="{{ $media->file_name }}"
                            @endif
                        >
                    @endif
                        {!! HTML::thumbnail($media, '32', '32', array('class' => 'preview')) !!}
                        <span>
                            <span>{{ $media->name }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
            @if($channel->medias->count() > 3)
                <li>
                    <a href="{{ url()->route('medias_explorer', ['profileType' => 'channel', 'profileId' => $channel->id]) }}" class="item">
                        <span class="others">+{{ $channel->medias->count()-3 }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif

<div class="content-sidebar-block">
    <div class="content-sidebar-line content-sidebar-titline">
        <span class="svgicon icon-members">
            @include('macros.svg-icons.members')
        </span>
        <h4 class="content-sidebar-title" title="{{ ucfirst(trans_choice('page.members', $channel->users()->count())) }}">{{ ucfirst(trans_choice('page.members', $channel->users()->count())) }}</h4>
    </div>
    <ul class="list-unstyled sidebar-users">
        @foreach($channel->validatedUsers as $member)
            <li>
                <a href="{{ $member->getUrl() }}" class="sidebar-users-line">
					{!! HTML::thumbImage($member->profile_media_id, 30, 30, [], 'user', 'avatar') !!}
                    <p class="name">{{ $member->getNameDisplay() }}</p>
                    @if($member->pivot->roles_id == 1)
                        <span class="label">Admin</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
