<div class="modal-header">
    <h4 class="modal-title">
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <ul class="list-unstyled modal-users-list">
    @foreach($viewers as $key => $view)
        <li>
            <a href="{{ $view->user->getUrl() }}">
                {!! HTML::thumbImage($view->user->profile_media_id, 60, 60, [], 'user', 'avatar') !!}
                <p class="name">{{ $view->user->getNameDisplay() }}</p>
            </a>
        </li>
    @endforeach
    </ul>
</div>