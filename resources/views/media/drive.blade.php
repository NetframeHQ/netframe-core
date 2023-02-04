<div class="modal fade" id="chooseFolder" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 90%">
    <div class="modal-content">
      {{Form::open(['route'=> ['medias_explorer', 'profileType' => $profileType, 'profileId' => $profileId], 'class' => 'no-auto-submit fn-submit-import'])}}
      <div class="modal-header">
        <h4 class="modal-title">{{ trans('xplorer.folder.import.title') }}</h4>
        <a class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">{{trans('form.close') }}</span>
        </a>
      </div>
      <div class="modal-body">
        <div class="netframe-grid-wrapper" id="fileXplorer" data-profile-type="{{ $profileType }}" data-profile-id="{{ $profileId }}" data-folder-id="{{ $idFolder }}">
          <ul class="netframe-list file-display">
          @if(isset($drive['folders']))
            @foreach($drive['folders'] as $folder)
              @include('media.xplorer.drive.choice')
            @endforeach
          @else
            <h3 class="text-muted text-center">{{ trans('xplorer.drive.emptyDrive') }}</h3>
          @endif
          </ul>
        </div>
        <div class="name">
          <div class="form-group">
            <label class="">Nom</label>
            <div class="input-group">
              <input class="folderName form-control" type="text" required name="name" value="" />
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <input class="folderId" type="hidden" name="id" value="0" />
        <button type="submit" name="all" class="folder btn btn-primary" disabled>{{ trans('xplorer.drive.importFolder') }}</button>
        <button type="submit" name="folder" class="all btn btn-primary">{{ trans('xplorer.drive.importAll') }}</button>
      </div>
      {{Form::close()}}
    </div>
  </div>
</div>
