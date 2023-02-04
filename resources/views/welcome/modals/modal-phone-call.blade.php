<div class="modal-header">
    <h4 class="modal-title text-center mb-2 mb-md-3" id="onboardingStartTitle">{{ trans('welcome.modals.modal1.title1') }}<strong>{{ trans('welcome.modals.modal1.title2') }}</strong></h4>
    <div class="text-center">
        @include('macros.svg-icons.new-user')
    </div>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <p class="text-center mb-5">{{ trans('welcome.modals.modal1.text1') }}</p>
    <button type="button" class="btn btn-primary btn-block mb-2 boarding-load-modal" data-href="{{ url()->route('welcome.modal.create.group') }}">{{ trans('welcome.modals.modal1b.btn1') }}</button>
    <p class="text-center mb-2">{{ trans('welcome.modals.or') }}</p>

    <p class="text-center mb-2">
        {{ trans('welcome.modals.modal1b.txt1') }} {{$phone}} {{ trans('welcome.modals.modal1b.txt2') }}
    </p>
</div>