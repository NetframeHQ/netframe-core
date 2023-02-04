<!-- <li class="mediaPanel col-xs-6 col-sm-4 col-lg-2" id="folder-{{ $folder->id }}" data-name="{{ $folder->name }}" data-type="folder" data-id="{{ $folder->id }}" data-confirm-message="{{ trans('xplorer.folder.confirmDelete') }}">
    <div class="driveFolder mediaThumbnail" data-name="{{ $folder->name }}" data-id="{{ $folder->id }}">

        <div class="text-center">
                <div class="preview-media">
                    <img src="{{ asset('assets/img/icons/folder.png') }}" class="img-responsive profile-image">
                </div>
                <span class="name">
                    <span>
                        {{ $folder->name }}
                    </span>
                </span>
                {{ \App\Helpers\DateHelper::xplorerDate($folder->created_at, $folder->updated_at) }}
        </div>
    </div>
</li> -->

<li id="folder-{{ $folder->id }}" data-name="{{ $folder->name }}" data-type="folder" data-id="{{ $folder->id }}" data-confirm-message="{{ trans('xplorer.folder.confirmDelete') }}" style="cursor: pointer;">
    <div class="item">
        <div class="item-icon">
            <span class="svgicon">
                @include('macros.svg-icons.doc')
            </span>
        </div>
        <div class="document-infos">
            <h4 class="document-title">
                @if($folder->default_folder == 0)
                    {{ $folder->name }}
                @else
                    {{ trans('xplorer.defaultFolders.'.$folder->name) }}
                @endif
            </h4>
            <p class="document-date">
                {{ \App\Helpers\DateHelper::xplorerDate($folder->created_at, $folder->updated_at) }}
            </p>
        </div>
    </div>
</li>
