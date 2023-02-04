<div class="tooltipContent">
	<div class="profile">
		<div>{!! HTML::thumbnail($profile->profileImage, 100, 100, [], asset('assets/img/avatar/user.jpg')) !!}</div>
		<h5>{{ucfirst($profile->getNameDisplay())}}</h5>
	</div>
    <div class="content-sidebar-action text-center">
        @if($profile->id != auth()->guard('web')->user()->id)
            {!! HTML::addFriendBtn(['author_id' => $profile->id,'user_from' => auth()->guard('web')->user()->id,'author_type'  => 'user'], \App\Friends::relation($profile->id) ) !!}
        @endif
        <a href="{{$profile->getUrl()}}" class="button primary button-subscribe">{{ trans('user.toProfil') }}</a>
        <br><br>
        @if($activeChannel && $profile->id != auth()->guard('web')->user()->id)
            <a href="{{ url()->route('channels.messenger', ['userId' => $profile->id]) }}" class="button primary counter button-discuss">
                <span class="default">
                    visio
                </span>
                <span class="content-link">
                    <span class="svgicon">
                        @include('macros.svg-icons.channel')
                    </span>
                    {{ trans('channels.sidebarUserLink') }}
                </span>
            </a>

            <a href="{{ url()->route('channels.livechat', ['channelId' => $profile->id, 'from' => 0, 'fromUser' => 1]) }}" class="button primary counter button-visio" target="_blank">
                <span class="default">
                    visio
                </span>
                <span class="content-link">
                    <span class="svgicon">
                        @include('macros.svg-icons.visio')
                    </span>
                    {{ trans('channels.startLive') }}
                </span>
            </a>
        @endif
    </div>
	<div class="content-sidebar-block">
        @if($profile->description != '')
            <div class="content-sidebar-line">
                <span class="svgicon icon-infos">
                    @include('macros.svg-icons.infos')
                </span>
                <h5 class="content-sidebar-title" title="{{ trans('user.description') }}">{{ trans('user.description') }}</h5>
                <p>{!! \App\Helpers\StringHelper::collapsePostText($profile->description) !!}</p>
            </div>
        @endif
        @if($profile->description != '')
            <div class="content-sidebar-line">
                <span class="svgicon icon-infos">
                    @include('macros.svg-icons.infos')
                </span>
                <h5 class="content-sidebar-title" title="{{ trans('user.training') }}">{{ trans('user.training') }}</h5>
                <p>{!! \App\Helpers\StringHelper::collapsePostText($profile->training) !!}</p>
            </div>
        @endif
        @php
            $fields = $profile->customFields();
        @endphp
        @foreach($fields as $values)
            @if($values['value'] != '')
            <div class="content-sidebar-line">
                <span class="svgicon icon-infos">
                    @include('macros.svg-icons.infos')
                </span>
                <h4 class="content-sidebar-title" title="{{ $values['name'] }}">{{ $values['name'] }}</h4>
                <p>{!! \App\Helpers\StringHelper::collapsePostText($values['value']) !!}</p>
            </div>
        @endif
        @endforeach
    </div>
</div>
