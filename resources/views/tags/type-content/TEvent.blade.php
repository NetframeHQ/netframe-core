<div class="tag-item">
<div class="panel ">
    <div class="panel-body">
        <h3 class="post-title mg-top-0">{{ $element->title }}</h3>
        <p><span class="icon ticon-event"></span>
            {{ \App\Helpers\DateHelper::eventDate($element->date, $element->time, $element->date_end, $element->time_end) }}
        </p>
        @if($element->location != null)
            <p> {{ $element->location }}</p>
        @endif
        <p>
            {!! \App\Helpers\StringHelper::collapsePostText($element->description) !!}
        </p>
    </div>
    <div class="panel-footer">
        <div class="media">
            <div class="media-left">
                {!! HTML::thumbImage($element->author->profile_media_id, 50, 50, ['class' => 'img-fluid'], $element->author->getType()) !!}
            </div>
            <div class="media-body">
                <p>
                   <a href="{{$element->author->getUrl()}}">
                        <span class="title1">{{ $element->author->getNameDisplay() }}</span>
                   </a>
                <br>
                <span class="datetime">
                    <date>{{ \App\Helpers\DateHelper::feedDate($element->created_at, $element->updated_at) }}</date>
                </span>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
