@foreach($results as $result)
    <li class="nf-list-setting"  id="notif-{{ $result->id }}">
        @if(!empty($result->notifLink))
            @if(is_array($result->notifLink))
                <a class="nf-invisiblink" 
                @foreach($result->notifLink as $key=>$value)
                    {{$key}}="{{$value}}"
                @endforeach
                ></a>
            @else
                <a class="nf-invisiblink"  href="{{ $result->notifLink }}"></a>
            @endif
        @endif
        <span class="svgicon">
            {!! $result->notifImg !!}
        </span>
        <div class="nf-list-infos">
            <div class="nf-list-title">
                {!! $result->notifTitle !!} {!! $result->notifTxt !!}
            </div>
            <span class="nf-list-subtitle">
                {!! $result->created_at->format('d/m/Y H:i') !!}
            </span>
        </div>
        @if(isset($result->notifRightImage))
            <span class="nf-actions">
                {!! $result->notifRightImage !!}
            </span>
        @endif
    </li>
@endforeach