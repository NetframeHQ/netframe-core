@if (count($profiles) > 0)
    <li class="help-block clearfix hidden-xs hidden-sm" id="user{{ucfirst($profileType)}}">
        <div class="panel panel-default text-center" id="mini-mosaic-{{ $profileType }}">
            <div class="panel-body mini-mosaic">
                <h3>
                    @if ($profileType == \Profile::TYPE_USER)
                        {{ trans('netframe.followers') }}
                    @else
                        {{ trans('netframe.'.$profileType) }}
                    @endif
                </h3>
                @foreach ($profiles AS $profile)
                    @include('components.thumbs.display')
                @endforeach
            </div>
        </div>
    </li>
@endif