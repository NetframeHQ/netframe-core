<div class="tag-item">
<div class="panel ">
    <div class="panel-body">
        {!! \App\Helpers\StringHelper::collapsePostText($element->content, 500) !!}
    </div>
    <div class="panel-footer">
        <div class="media">
            <div class="media-left">
                {!! HTML::thumbImage($element->author->profile_media_id, 50, 50, ['class' => 'img-fluid'], $element->author->getType()) !!}
            </div>
            <div class="media-body">
                <p>
                   <a href="{{$element->author->getUrl()}}">
                        <span class="icon ticon-{{ $element->author->getType() }}"></span>
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
