<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('xplorer.file.archiveTitle') }} {{ $media->name }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">

    <ul>
        @foreach($archives as $archive)
            <li>
                <a href="{{ url()->route('media.download.archive', ['id' => $archive->id ]) }}">
                    {{ $archive->name }} 
                </a>
                ( {{ $archive->updated_at->format('d/m/Y - H:i:s') }} )
            </li>
        @endforeach
    </ul>
</div>
<!-- End MODAL-BODY -->