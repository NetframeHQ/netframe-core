@if( count($playlistsuser) > 0)
<div class="mg-bottom hidden-lg hidden-md">
    <div class="col-xs-12 text-center mg-bottom">
        <a href="{{ url()->route('sidebar.user.widget', [ $dataUser->id, 'playlists' ] ) }}" class="btn btn-border-default col-xs-12" data-toggle="modal" data-target="#modal-ajax">
            {{ trans('widgets.ownplaylists') }}
        </a>
    </div>
</div>
@endif