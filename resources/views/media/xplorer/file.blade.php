<li class="container @if($media->read_only == 0) draggable @endif media-file" data-name="{{ $media->name }}" data-type="media" data-type="file" data-type-mime="{{ $media->mime_type }}" data-id="{{ $media->id }}" id="file-{{ $media->id }}" data-confirm-message="{{ trans('xplorer.file.confirmDelete') }}">
    <div class="item">
        @if($media->isDocument())
            @php
            if($media->isDocument() && $activeOffice && "application/pdf" !== $media->mime_type && $rights < 5) {
                $link = url()->route('office.document', array('documentId' => $media->id));
                $download = false;

            /* ouvre les docs office avec le lecteur pdf */
            } elseif ($media->isDocument() && !$activeOffice && $media->feed_path != null) {
                $link = url()->route('media.pdf.viewer').'?file='.urlencode(URL::route('media_download', ['id' => $media->id, 'feed' => true]));
                $download = false;

            /* Ouvre un PDF avec la visionneuse */
            } elseif ("application/pdf" === $media->mime_type) {
                $link = url()->route('media.pdf.viewer').'?file='.URL::route('media_download', ['id' => $media->id]);
                $download = false;

            /* Télécharge le document */
            }else {
                $link = url()->route('media_download', array('id' => $media->id));
                $download = true;
            }
            @endphp
            <a href="{{ $link }}" target="_blank" class="nf-invisiblink" @if($download) download @endif>
        @elseif (!$media->isTypeDisplay())
            <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="nf-invisiblink" download>
        @else
            <a class="viewMedia nf-invisiblink"
                data-media-name="{{ $media->name }}"
                data-media-id="{{ $media->id }}"
                data-media-type="{{ $media->type }}"
                data-media-platform="{{ $media->platform }}"
                data-media-mime-type="{{ $media->mime_type }}"

                @if ($media->platform !== 'local')
                    data-media-file-name="{{ $media->file_name }}"
                @endif
            >
        @endif
        </a>

        @if($rights && $rights <= 4 && $profileType !='user' && $profileType !='channel' && $media->read_only == 0)
            <a class="fn-star-media svgicon icon-fav {{ ($media->pivot->favorite == 1) ? 'active' : '' }}">
                @include('macros.svg-icons.star')
            </a>
        @endif

        @if($media->read_only == 1)
            <span class="locked-media svgicon">
                @include('macros.svg-icons.lock')
            </span>
        @endif



        @if($media->type == \App\Media::TYPE_DOCUMENT)
            <div class="doc-preview">
        @elseif (!$media->isTypeDisplay())
            <div class="item-icon">
        @else
            <div class="item-preview">
        @endif
            {!! HTML::thumbnail($media, '', '', []) !!}
        </div>

        <div class="document-infos">
            <h4 class="document-title">
                {{ $media->name }}
            </h4>
            <p class="document-date">
                {{ \App\Helpers\DateHelper::xplorerDate($media->created_at, $media->updated_at) }}
            </p>
        </div>

        @include('media.partials.menu-actions')
    </div>
</li>
