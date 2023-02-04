@if(count($likes)>0)
    @foreach($likes as $emoji)
        <div class="nf-react">
            <a class="nf-btn" href="{{ url()->route('post.likers', ['elementType' => $liked_type, 'elementId' => $liked_id]) }}" data-toggle="modal" data-target="#modal-ajax-thin">
                <span class="btn-img emoji">{{$emoji->value}}</span>
                <span class="btn-txt btn-digit">{{$emoji->total}}</span>
            </a>
            <div class="nf-tooltip">
                <div class="tooltip-list">
                    <ul>
                        @php
                            $authors = \App\Like::where('emojis_id', $emoji->id)
                            ->where('liked_id', $liked_id)
                            ->where('liked_type', "App\\".$liked_type)
                            ->limit(5)
                            ->get();
                        @endphp
                        @foreach($authors as $author)
                        <li><a href="{{$author->liker->getUrl()}}">{{$author->liker->getNameDisplay()}}</a></li>
                        @endforeach
                    </ul>
                </div>
                @if($likes->count() <= 1)
                    <p class="tooltip-txt">{{trans('netframe.reactedToThisPostSingular')}}</p>
                @else
                    <p class="tooltip-txt">{{trans('netframe.reactedToThisPost')}}</p>
                @endif
            </div>
        </div>
    @endforeach
@endif