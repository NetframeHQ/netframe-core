<div class="panel-document">
    <div class="panel-document-head">
        <a href="{{ $profile->getUrl() }}" class="nf-invisiblink" title="{{ trans('channels.display.viewProfile') }}"></a>
        <div class="panel-document-icon">
            {!! HTML::thumbImage($profile->profile_media_id, 60, 60, [], $profile->getType()) !!}
        </div>
        <div class="panel-document-info">
            <h3 class="panel-document-title">{{ $profile->getNameDisplay() }}</h3>
        </div>
    </div>
</div>
