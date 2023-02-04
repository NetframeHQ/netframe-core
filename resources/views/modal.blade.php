<div class="modal fade" id="modalbox" role="dialog" tabindex="-1" aria-labelledby="modaboxLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{ trans('form.close') }}</span>
                </button>
            </div>
            <!-- End MODAL-HEADER -->

            <div class="modal-body">{{-- @include('page.form-publish') --}}</div>
            <!-- End MODAL-BODY -->

            <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('form.close') }}</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div> -->
            <!-- End MODAL-FOOTER -->
        </div>
    </div>

</div>
<!-- End MODALBOX -->