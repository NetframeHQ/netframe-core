<li class="container @if($folder->default_folder == 0) droppable draggable @endif"
    id="folder-{{ $folder->id }}"
    data-name="{{ $folder->name }}"
    data-type="folder"
    data-id="{{ $folder->id }}"
    data-confirm-message="{{ trans('xplorer.folder.confirmDelete') }}"
    data-confirm-workflow="{{ trans('xplorer.folder.containWorkflows') }}"
    data-workflows="{{ $folder->hasWorkflowFiles()->count() }}"
    >
    <div class="item">
        <a href="{{ $folder->getUrl() }}" class="nf-invisiblink"></a>
        @if($folder->default_folder == 0)
            <ul class="nf-actions">
                <li class="nf-action">
                    <a href="#" class="nf-btn btn-ico btn-submenu">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.menu')
                        </span>
                    </a>
                    <div class="submenu-container submenu-right">
                        <ul class="submenu">
                            <li>
                                <a
                                    href="{{ url()->route('xplorer_copy_element', ['profileType' => $profileType, 'profileId' => $profileId, 'elementType' => 'folder', 'elementId' => $folder->id]) }}"
                                    class="nf-btn"
                                    data-toggle="modal"
                                    data-target="#modal-ajax"
                                >
                                    <span class="btn-txt">
                                        {{ trans('xplorer.folder.menu.copy') }}
                                    </span>
                                </a>
                            </li>
                            @if($rights && $rights <= 4)
                                <li>
                                    <a
                                        href="{{ url()->route('xplorer_edit_folder', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $folder->id]) }}?parent={{ $idFolder }}"
                                        class="fn-add-folder nf-btn"
                                        data-toggle="modal"
                                        data-target="#modal-ajax"
                                    >
                                        <span class="btn-txt">
                                            {{ trans('xplorer.folder.menu.update') }}
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ url()->route('xplorer_move_element', ['profileType' => $profileType, 'profileId' => $profileId, 'elementType' => 'folder', 'elementId' => $folder->id]) }}"
                                        class="nf-btn"
                                        data-toggle="modal"
                                        data-target="#modal-ajax"
                                    >
                                        <span class="btn-txt">
                                            {{ trans('xplorer.folder.menu.move') }}
                                        </span>
                                    </a>
                                </li>
                                <li class="sep"></li>
                                <li>
                                    <a href="#" class="fn-delete-xplorer nf-btn">
                                        <span class="btn-txt">
                                            {{ trans('xplorer.folder.menu.delete') }}
                                        </span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            </ul>
        @endif

        <div class="item-icon">
            <span class="svgicon">
                @include('macros.svg-icons.doc')
            </span>
        </div>
        <div class="document-infos">
            <h4 class="document-title">
                {{ $folder->getNameDisplay($profile) }}
            </h4>
            <p class="document-date">
                {{ \App\Helpers\DateHelper::xplorerDate($folder->created_at, $folder->updated_at) }}
            </p>
        </div>
    </div>
</li>
