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
					<a href="{{ url()->route('media.details', ['id' => $media->id]) }}" data-toggle="modal" data-target="#modal-ajax" class="nf-btn">
						<span class="btn-txt">
							{{ trans('xplorer.file.menu.details') }}
						</span>
					</a>
				</li>
                <li>
                    <a class="nf-btn" href="{{ url()->route('media_download', ['id' => $media->id]) }}">
                        <span class="btn-txt">
                            {{ trans('xplorer.file.menu.download') }}
                        </span>
                    </a>
                </li>
                @if($rights && $rights <= 2)
                    <li>
                        @if($media->read_only == 0)
                            <a href="#" class="nf-btn fn-modify-lock" data-media-id="{{ $media->id }}" data-new-state="lock">
                                <span class="btn-txt">
                                    {{ trans('xplorer.file.menu.lock') }}
                                </span>
                            </a>
                        @else
                            <a href="#" class="nf-btn fn-modify-lock" data-media-id="{{ $media->id }}" data-new-state="unlock">
                                <span class="btn-txt">
                                    {{ trans('xplorer.file.menu.unlock') }}
                                </span>
                            </a>
                        @endif
                    </li>
                @endif
                <li class="sep"></li>

                @if ("application/pdf"===$media->mime_type)
                    <li>
                        <a href="{{ url()->route('media.pdf.viewer') }}?file={{ URL::route('media_download', ['id' => $media->id]) }}" target="_blank" class="nf-btn">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.pdfopen') }}
                            </span>
                        </a>
                    </li>
                @endif
                @if($media->feed_path && $media->isDocument())
                    <li>
                        <a href="{{ url()->route('media.pdf.viewer') }}?file={{ urlencode(URL::route('media_download', ['id' => $media->id, 'feed' => true])) }}" target="_blank" class="nf-btn">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.pdfview') }}
                            </span>
                        </a>
                    </li>
                @endif
                @if(isset($openLocation) && $openLocation && $media->getFolderUrl() != null)
                    <li>
                        <a class="nf-btn" href="{{ $media->getFolderUrl() }}">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.openFolder') }}
                            </span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="#" class="nf-btn fn-copy-media-link" data-href="{{ $media->getUrl() }}">
                        <span class="btn-txt">
                            {{ trans('xplorer.file.menu.copyLink') }}
                        </span>
                    </a>
                </li>
                <li>
                    <a class="nf-btn" href="{{ url()->to('netframe/form-share-media', ['mediaId' => $media->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                        <span class="btn-txt">
                            {{ trans('xplorer.file.menu.share') }}
                        </span>
                    </a>
                </li>


                <li class="sep"></li>

                {{--@if(in_array(".".pathinfo($media->file_path, PATHINFO_EXTENSION),config('office.DOC_SERV_EDITED')) && $activeOffice)--}}
                @if($media->isDocument() && $activeOffice && "application/pdf"!==$media->mime_type)
                    <li>
                        <a class="nf-btn" href="{{ url()->route('office.document', array('documentId' => $media->id)) }}" target="_blank">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.edit') }}
                            </span>
                        </a>
                    </li>
                @endif

                <li>
                    <a class="nf-btn" href="{{ url()->route('xplorer_copy_element', ['profileType' => $profileType, 'profileId' => $profileId, 'elementType' => 'media', 'elementId' => $media->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                        <span class="btn-txt">
                            {{ trans('xplorer.file.menu.copy') }}
                        </span>
                    </a>
                </li>

                @if($rights && $rights <= 4 && $media->read_only == 0)
                    <li>
                        <a class="nf-btn" href="{{ url()->route('xplorer_edit_file', ['idFile' => $media->id]) }}" data-toggle="modal" data-target="#modal-files">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.update') }}
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="nf-btn" href="{{ url()->route('xplorer_move_element', ['profileType' => $profileType, 'profileId' => $profileId, 'elementType' => 'media', 'elementId' => $media->id]) }}"
                        data-toggle="modal" data-target="#modal-ajax">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.move') }}
                            </span>
                        </a>
                    </li>
                    <li class="sep"></li>
                    <li>
                        <a href="#" class="nf-btn fn-delete-xplorer">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.delete') }}
                            </span>
                        </a>
                    </li>
                @endif
                @if(count($media->archives) > 0)
                    <li>
                        <a class="nf-btn" href="{{ url()->route('xplorer.media.archives', ['mediaId' => $media->id]) }}" data-target="#modal-ajax" data-toggle="modal">
                            <span class="btn-txt">
                                {{ trans('xplorer.file.menu.archives') }}
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </li>
</ul>