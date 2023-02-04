@if(session()->has('autoFireModal'))
     {{ session('autoFireModal') }}
@endif

<!-- MODAL POST -->
<div class="modal fade modal-emptyable" id="modal-ajax" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
            <!-- End MODAL-BODY -->
        </div>
    </div>

</div>
<!-- End MODALBOX -->

<!-- MODAL CHARTER -->
@if(isset($need_local_consent_content))
    <div class="modal fade modal-emptyable" id="modal-ajax-charter" role="dialog" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            {!! trans('boarding2020.consentCharter') !!}
                        </h4>
                    </div>
                    <div class="modal-body-content">
                        {!! $need_local_consent_content !!}
                        <div class="text-center">
                            <a href="{{ url()->route('boarding.user.accept.charter') }}" class="button primary">{{ trans('boarding2020.consentCharterValid') }}</a>
                        </div>
                    </div>
                </div>
                <!-- End MODAL-BODY -->
            </div>
        </div>

    </div>
@endif
<!-- End MODALBOX -->

<!-- MODAL CONFIRM -->
<div class="modal fade modal-emptyable" id="modal-confirm" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
            <!-- End MODAL-BODY -->
            <div class="modal-footer">
                <a class="fn-confirm">{{ trans('netframe.confirm.accept') }}</a>
                <a class="fn-decline">{{ trans('netframe.confirm.decline') }}</a>
            </div>
        </div>
    </div>

</div>
<!-- End MODALBOX -->

<!-- MODAL CONFIRM DELETE -->
<div class="modal fade modal-emptyable" id="modal-confirm-delete" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                </h4>
                <a class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{trans('form.close') }}</span>
                </a>
            </div>
            <div class="modal-body text-center p-2"></div>
            <!-- End MODAL-BODY -->
            <div class="modal-footer">
                <a href="#"
                    class="btn fn-confirm-modal-delete">
                    {{ trans('netframe.confirm.accept') }}
                </a>
                <a href="#" class="btn fn-decline-modal-delete" data-dismiss="modal">{{ trans('netframe.confirm.decline') }}</a>
            </div>
        </div>
    </div>

</div>
<!-- End MODALBOX -->

<!-- MODAL THIN -->
<div class="modal fade modal-emptyable" id="modal-ajax-thin" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
            <!-- End MODAL-BODY -->
        </div>
    </div>

</div>
<!-- End MODALBOX -->

<!-- MODAL VIEW MEDIA -->
<div class="modal fade" id="viewMediaModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <div class="modal-carousel">
                <div class="carousel-item active"></div>
            </div>
            <div class="modal-infos">
            </div>
        </div>
    </div>
</div>
<!-- End MODALBOX -->

<!-- MODAL FILES -->
<div class="modal fade modal-emptyable" id="modal-files" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<!-- End MODALBOX -->

<!-- MODAL POST -->
<div class="modal fade modal-ajax-comments modal-emptyable" id="modal-ajax-comment" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
            <!-- End MODAL-BODY -->
        </div>
    </div>

</div>
<!-- End MODALBOX -->

<!-- MODAL POST -->
<div class="modal fade modal-ajax-comments modal-emptyable" id="modal-ajax-comment2" role="dialog" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
            <!-- End MODAL-BODY -->
        </div>
    </div>

</div>
<!-- End MODALBOX -->