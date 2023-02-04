<div
    class="tl-publish-as nf-publishas"
    data-target-form="{{ $targetForm }}"
    data-postfix="{{ $inputTarget['postfix'] }}"
    data-target-input-id="{{ $inputTarget['id'] }}"
    data-target-input-type="{{ $inputTarget['type'] }}"
    data-secondary="{{ (isset($inputTarget['secondary'])) ? '1' : '0' }}"
>
    <div href="#" class="nf-btn btn-submenu tl-display-as">
        @if($oldProfile != null)
            {!! HTML::thumbImage(
                $oldProfileObject->profile_media_id,
                30,
                30,
                [],
                $oldProfileObject->getType(),
                'btn-img',
                $oldProfileObject
            ) !!}
            <div class="btn-txt">
                {{ $oldProfileObject->getNameDisplay() }}
            </div>
        @else
            {!! HTML::thumbImage(
                auth()->guard('web')->user()->profile_media_id,
                30,
                30,
                [],
                auth()->guard('web')->user()->getType(),
                'btn-img',
                auth()->guard('web')->user()
            ) !!}
            <div class="btn-txt">
                {{ auth()->guard('web')->user()->getNameDisplay() }}
            </div>
        @endif
        <div class="btn-img svgicon">
            @include('macros.svg-icons.arrow-down')
        </div>
    </div>
    <div class="submenu-container submenu-left">
        <ul class="submenu">
            @if(isset($profiles))
                @foreach($profiles as $keyProfile => $items)
                    @if(${$keyProfile.'Permit'})
                        @foreach($items as $key => $item)
                            @if((($userPermit && $keyProfile == 'user') || ($keyProfile != 'user')) &&
                                ((isset($inputTarget['secondary']) && $item['role'] < 4) || (!isset($inputTarget['secondary']) && $item['role'] <= 4)))
                                <li>
                                    <a href="#post-has" class="f-publish-as nf-btn" data-profile="{{ $keyProfile }}" data-profile-id="{{ $item['id'] }}">
                                        @php
                                            $currentProfile = unserialize($item['profile']);
                                        @endphp
                                        {!! HTML::thumbImage(
                                            $currentProfile->profile_media_id,
                                            30,
                                            30,
                                            [],
                                            $currentProfile->getType(),
                                            'btn-img',
                                            $currentProfile
                                        ) !!}
                                        <span class="btn-txt">
                                            {{ $item['name'] }}
                                        </span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
            @if(isset($channels))
                @foreach($channels as $id=>$name)
                    <li>
                        <a href="#post-has" class="f-publish-as nf-btn" data-profile="channel" data-profile-id="{{ $id }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.channel')
                            </span>
                            <span class="btn-txt">
                                {{ $name }}
                            </span>
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>