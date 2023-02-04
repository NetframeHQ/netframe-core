<li class="container" id="folder-{{ $folder->id }}" data-name="{{ $folder->name }}" data-type="driveFolder" data-id="{{ json_encode(['folder' => $idFolder, 'drive' => $folder->id]) }}" data-confirm-message="{{ trans('xplorer.folder.drive.confirmDelete') }}">
    <div class="item">
        <a href="{{ url()->route('medias_explorer', ['profileType' => $profileType, 'profileId' => $profileId, 'folder' => $idFolder, 'driveFolder' => $folder->id]) }}" class="nf-invisiblink"></a>
        <div class="item-icon">
            <span class="svgicon">
                @include('macros.svg-icons.doc')
            </span>
        </div>
        <div class="document-infos">
            <h4 class="document-title">
                {{ $folder->name }}
            </h4>
            <p class="document-date">
                {{ \App\Helpers\DateHelper::xplorerDate($folder->created_at, $folder->updated_at) }}
            </p>
        </div>
    </div>
</li>
