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
    <button type="button" class="btn btn-primary btn-block mb-2 boarding-load-modal" data-href="{{ url()->route('welcome.modal.create.group') }}">{{ trans('welcome.modals.modal1.btn1') }}</button>
    <p class="text-center mb-2">{{ trans('welcome.modals.or') }}</p>

    <button type="button" class="btn btn-secondary btn-block load-form-call">{{ trans('welcome.modals.modal1.btn2') }}</button>
    {{ Form::open(['route' => 'welcome.modal.call.back', 'class' => 'd-none', 'id' => 'form-phone']) }}
        <div class="form-group mb-2">
            {{ Form::label('phone', trans('welcome.modals.modal1.yourPhone') ) }}
            {{ Form::text('phone', '', ['class' => 'form-control mb-2', 'placeholder' => trans('welcome.modals.modal1.phonePlaceholder') ]) }}
        </div>
        <button class="btn btn-primary btn-block" type="submit" >{{ trans('welcome.modals.modal1.askCall') }}</button>
    {{ Form::close() }}
</div>