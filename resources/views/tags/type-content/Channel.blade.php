<div class="tag-item">
<div class="panel ">
    <div class="panel-body">
        <div class="media">
            <div class="media-left">
                {!! HTML::thumbImage($element->profile_media_id, 50, 50, ['class' => 'img-fluid'], $element->getType()) !!}
            </div>
            <div class="media-body">
                <p>
                   <a href="{{$element->getUrl()}}">
                        <span class="title1">{{ $element->getNameDisplay() }}</span>
                   </a>
                   @if($element->description != null)
                       <p>
                            {{ $element->description }}
                       </p>
                   @endif
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
