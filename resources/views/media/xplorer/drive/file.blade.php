<li class="container media-file" data-name="{{ $media->name }}" data-type="media" data-type="driveFile" data-drive="{{$idFolder}}" data-id="{{ json_encode(['folder' => $idFolder, 'drive' => $media->id]) }}" id="file-{{ $media->id }}" data-confirm-message="{{ trans('xplorer.file.drive.confirmDelete') }}">
    <div class="item">
        {{-- @if (!$media->isTypeDisplay()) --}}
            <a href="{{ $media->file_path }}" class="nf-invisiblink" target="_blank">
        {{-- @else
            <a class="nf-invisiblink" target="_blank"
                data-media-name="{{ $media->name }}"
                data-media-id="{{ $media->id }}"
                data-media-type="{{ $media->type }}"
                data-media-platform="{{ $media->platform }}"
                data-media-mime-type="{{ $media->mime_type }}"

                @if ($media->platform !== 'local')
                    data-media-file-name="{{ $media->file_name }}"
                @endif
            >
        @endif --}}
        </a>

        @if (!$media->isTypeDisplay())
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
    </div>
</li>
