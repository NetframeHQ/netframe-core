<div class="modal fade" id="autoFireModal">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">
                &nbsp;
            </h4>
            <a class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('form.close') }}</span>
            </a>
        </div>
        <!-- End MODAL-HEADER -->

        <div class="modal-body">
            @include('lang.'.Lang::locale().'.welcome')
        </div>
        <!-- End MODAL-BODY -->

        <div class="modal-footer">

        </div>
    </div>
</div>
</div>