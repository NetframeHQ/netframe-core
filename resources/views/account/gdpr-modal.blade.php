@if(isset($agrementSent))
    <div class="modal-body p-4">
        <p class="text-center">
            {{ trans('auth.gdpr_agrement_recorded') }}
            <br><br>
            <a data-dismiss="modal" href="#" class="button primary">{{ trans('netframe.close') }}</a>
        </p>
    </div>
@else
    <div class="modal-header">
        <h4 class="modal-title">
            {{ trans('user.menu.privacySettings') }}
        </h4>
        <a class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">{{trans('form.close') }}</span>
        </a>
    </div>
    <div class="modal-body p-4">
        <p>
            {{ trans('auth.gdpr_text') }}
        </p>
        <p>
            <ul class="list-inline text-center">
                <li class="list-inline-item"><a href="#" class="fn-accept-gdpr button secondary">{{ trans('auth.accept_gdpr') }}</a></li>
                <li class="list-inline-item">-</li>
                <li class="list-inline-item"><a href="#" data-dismiss="modal" class="button secondary">{{ trans('auth.decline_gdpr') }}</a></li>
            </ul>
        </p>
        <p class="text-center">
            {{ trans('auth.gdpr_in_account') }}
        </p>
    </div>
@endif