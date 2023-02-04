<div class="tag-item">
<div class="panel ">
    <div class="panel-body">
        <h3 class="post-title mg-top-0">{{ $element->name }}</h3>
        @if($element->location != null)
            <p><span class="icon ticon-geoloc"></span> {{ $element->location }}</p>
        @endif
        <p>
            {!! \App\Helpers\StringHelper::collapsePostText($element->content) !!}
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
