<li class="container item media-file" data-name="{{ $media->name }}" data-type="media" data-type="file" data-id="{{ $media->id }}" id="file-{{ $media->id }}">
    <a href="{{ url()->route('office.document', array('documentId' => $media->id)) }}" class="nf-invisiblink" target="_blank"></a>

    @if($media->read_only == 1)
        <span class="locked-media svgicon">
            @include('macros.svg-icons.lock')
        </span>
    @endif
    <div class="menu-wrapper" style="opacity: .5">
        {{ \App\Helpers\DateHelper::xplorerDate($media->created_at, $media->updated_at) }}
    </div>

    <div class="item-icon">
        {!! HTML::thumbnail($media, '', '', []) !!}
    </div>
    <div class="document-infos">
        <h4 class="document-title">
            {{ $media->name }}
        </h4>
    </div>
</li>
