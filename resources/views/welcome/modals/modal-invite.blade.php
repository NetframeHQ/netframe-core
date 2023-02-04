<div class="modal-header">
    <h4 class="modal-title text-center mb-2 mb-md-3" id="invitationTitle">{{ trans('welcome.modals.modal3.title1') }} <strong>{{ trans('welcome.modals.modal3.title2') }}</strong><br> {{ trans('welcome.modals.modal3.title3') }}</strong></h4>
    <div class="text-center">
        @include('macros.svg-icons.send-invite')
    </div>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">

    @if(isset($sendInvit) && $sendInvit)

        <div class="text-center">
            {{ trans('welcome.modals.modal3.inviteSended') }}
        </div>
        <br><br>

        <button class="btn btn-primary btn-block"  data-dismiss="modal" data-toggle="modal" data-target="#toolNav" data-backdrop="">{{ trans('boarding2020.next') }}</button>
    @else
        {{ Form::open(['route' => 'instance.invite', 'class' => 'box mb-2', 'id' => 'invite-users']) }}
            {{ Form::hidden('nbFields', '2') }}
            <div class="form-group mb-2 emails-list">
                {{ Form::label('email1', trans('welcome.modals.modal3.collabEmail') ) }}
                {{ Form::text('email[]', '', ['class' => 'form-control mb-2', 'placeholder' => trans('welcome.modals.modal3.emailPlaceholder') ]) }}
                {{ Form::text('email[]', '', ['class' => 'form-control mb-2', 'placeholder' => trans('welcome.modals.modal3.emailPlaceholder') ]) }}
                <div class="invalid-feedback">Error message</div>
            </div>

            <button class="btn btn-outline-light mb-3 add-email" data-nb="2">
                @include('macros.svg-icons.plus-circle')
                {{ trans('welcome.modals.modal3.addEmail') }}
            </button>

            <button class="btn btn-primary btn-block" type="submit" >{{ trans('welcome.modals.modal3.btnInvit') }}</button>
        {{ Form::close() }}

        <div class="box box--link">
            <p class="mb-2">{{ trans('welcome.modals.modal3.shareLink') }}</p>
            <button class="btn btn-outline-light boarding-link copyKey">
                @include('macros.svg-icons.link')
                <span>{{ $instance->getBoardingUrl() }}</span>
            </button>
            <input type="text" id="hiddenKey" value="{{$instance->getBoardingUrl()}}" style="position: absolute; z-index: -999; opacity: 0;">
            <p class="text-center mb-0"><a class="text-white copyKey">{{ trans('welcome.modals.modal3.linkCopy') }}</a></p>
        </div>
        <p class="text-center mt-4 mb-0"><button class="text-white skip" data-dismiss="modal" data-toggle="modal" data-target="#toolNav" data-backdrop="">{{ trans('boarding2020.skip') }}</button></p>
    @endif
</div>