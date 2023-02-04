<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('billing.overQuota.title') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">

    <p class="text-center">
        {{ trans('billing.overQuota.'.$overQuota) }} ( {{ $GBquota }} {{ trans('billing.overQuota.unit') }} )
    </p>

    <p class="text-center">
    @if($role < 3)
        {{ trans('billing.overQuota.getMore.'.session('instanceOffer').'.text') }}
    @else
        {{ trans('billing.overQuota.getMore.user') }}
    @endif
    </p>
</div>
<!-- End MODAL-BODY -->


