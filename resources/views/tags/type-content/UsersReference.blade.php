<div class="tag-item">
<div class="panel ">
    <div class="panel-body">
        <div class="media">
            <div class="media-left">
                {!! HTML::thumbImage($element->user->profile_media_id, 50, 50, ['class' => 'img-fluid'], $element->user->getType()) !!}
            </div>
            <div class="media-body">
                <p>
                   <a href="{{$element->user->getUrl()}}">
                        <span class="title1">{{ $element->user->getNameDisplay() }}</span>
                   </a>
                <br>
                <span class="datetime">
                    <date>{{ \App\Helpers\DateHelper::feedDate($element->user->created_at, $element->user->updated_at) }}</date>
                </span>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
