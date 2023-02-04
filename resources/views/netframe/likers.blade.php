<div class="modal-header">
    <h4 class="modal-title">
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <ul class="nav nav-tabs">
        @foreach($emojis as $key=>$emoji)
        <li class="nav-item">
            <a class="nav-link @if($key==0) active @endif" data-toggle="tab" href="#pane{{$emoji->emoji->id??''}}">
                @if($emoji->emoji!=null)
                <span style="padding: 5px">
                    {{$emoji->emoji->value}}
                </span>
                @else
                <span class="icon-like">
                    @include('macros.svg-icons.like')
                </span>
                @endif
                <span style="float: right;">{{$emoji->total}}</span>
            </a>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
    <div class="tab-pane container active" id="pane{{$likers[0]->emoji->id??''}}">
        <ul class="list-unstyled modal-users-list">
    @php $old = $likers[0]->emoji; @endphp
    @foreach($likers as $key => $liker) 
        @if($old!=$liker->emoji??null)
            </ul></div>
            @if(isset($likers[$key]))
            <div class="tab-pane container" id="pane{{$liker->emoji->id??''}}">
            <ul class="list-unstyled modal-users-list">
            @endif
        @endif
        <li>
            <a href="{{ $liker->liker->getUrl() }}">
                {!! HTML::thumbImage($liker->liker->profile_media_id, 60, 60, [], 'user', 'avatar') !!}
                <p class="name">{{ $liker->liker->getNameDisplay() }}</p>
            </a>
        </li>
        @php $old = $liker->emoji; @endphp
    @endforeach
        </ul>
    </div>
    </div>
</div>