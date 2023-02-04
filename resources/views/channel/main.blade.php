@if($activeChannel)
    <channels>
        <div class="fn-menu">
            <div class="sidebar-title">
                <h3 title="{{ trans('channels.feeds.title') }}">{{ trans('channels.feeds.title') }}</h3>
                @if(!auth()->user()->visitor)
                    <a class="nf-btn btn-ico btn-nobg" href="{{ url()->route('channel.edit') }}" alt="" title="{{ trans('channels.dropdown.new') }}">
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.plus')
                        </span>
                    </a>
                    <a class="nf-btn btn-ico btn-nobg" href="{{ url()->route('channels.my.feeds') }}" alt="" title="{{ trans('channels.dropdown.manage') }}">
                        <span class="btn-img svgicon icon">
                            @include('macros.svg-icons.settings-xs')
                        </span>
                    </a>
                @endif
            </div>
            <feeds>
                <ul class="sidebar-links list-unstyled" id="channels-list">
                    @foreach($channels as $channel)
                        @include('channel.partials.channel-link')
                    @endforeach
                    @if($channels->count() > 3 && session('activeChannels') == false)
                        <li>
                            <a class="more fn-more-sidebar-link" data-target="channels-list" href="#" title="{{trans('page.viewMoreChannel')}} ({{$channels->count() - 3}})">
                                <span>{{ trans('page.viewMoreChannel') }} (<span class="num">{{ $channels->count() - 3 }}</span>)</span>
                            </a>
                        </li>
                    @endif
                    @if($channels->count() < 3 && !auth()->user()->visitor)
                        <li>
                            <a href="{{ url()->route('channel.edit') }}" title="{{ trans('channels.dropdown.new') }}">
                                <span class="svgicon icon-talkentity">
                                    @include('macros.svg-icons.add')
                                </span>
                                <span class="txt">{{ trans('channels.dropdown.new') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>

                <hr>

                <ul class="sidebar-users list-unstyled" id="users-list">
                    @if(isset($currentChannel))
                         @include('channel.partials.channel-link', ['channel' => $currentChannel])
                    @endif
                    @foreach($personnalChannels as $channel)
                        @include('channel.partials.channel-link')
                    @endforeach
                    @if($personnalChannels->count() > 3 && session('activeChannels') == false)
                        <li>
                            <a class="more fn-more-sidebar-link" title="{{trans('page.viewMoreChannel')}} ({{$personnalChannels->count() - 3}})" data-target="users-list" href="#">
                                <span>{{ trans('page.viewMoreChannel') }} (<span class="num">{{ $personnalChannels->count() - 3 }}</span>)</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </feeds>
        </div>


            @if(!auth()->user()->visitor)
            <div class="navbar-form search-user-sidebar">
                {{--
                <a href="#" class="fn-display-search">
                    <span class="svgicon">
                        @include('macros.svg-icons.search')
                    </span>
                </a>
                --}}
                {{ Form::input('text', 'query', null, ['class' => 'form-control', 'placeholder' => trans('channels.searchUsers'), 'id' => 'fn-search-contact', 'autocomplete' => 'off']) }}
            </div>
            <div id="display-users-results" class="sidebar-nav"></div>
            @endif
    </channels>
@endif