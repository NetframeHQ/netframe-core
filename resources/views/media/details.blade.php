<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('xplorer.file.menu.details') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">
    <div>
        @foreach($views as $view)
            <p>
                <div>
                    {{$view->user->getNameDisplay()}} {{ trans('xplorer.details.type-'.$view->type)}} {{trans('xplorer.details.the-file')}}
                    <div class="float-right">{{\App\Helpers\DateHelper::xplorerDate($view->created_at)}}</div>
                </div>

            </p>
        @endforeach
    </div>

</div>
