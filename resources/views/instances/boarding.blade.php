@extends('instances.main')


@section('subcontent')
    <div class="nf-form nf-col-2">
        <!-- <div class="nf-settings-title">
            {{ trans('netframe.welcome') }}, Gi.
        </div> -->
        @if(isset($result))
            <div class="nf-form-informations bg-success">
                {{ trans('instances.boarding.result.'.$result) }}
            </div>
        @endif

        <div class="nf-settings-title">
            {{ trans('instances.boarding.publicUrlTitle') }}
        </div>

        <!-- ACCESS LINK -->
        <div class="nf-form-informations">
            {{ trans('instances.boarding.publicUrl') }}
            <code>
                {{ $instance->getUrl() }}
            </code>
            <hr>
            {{ trans('instances.boarding.publicKey') }}
            <code>
                {{ $instance->getParameter('boarding_invite_key') }}
            </code>
            <ul class="nf-actions">
                <li class="nf-action">
                    <a href="{{ url()->route('instance.boarding', ['action' => 'generate-key']) }}" class="nf-btn">
                        <span class="btn-txt">
                            {{ trans('instances.boarding.keyRegenerate') }}
                        </span>
                    </a>
                </li>
            </ul>
            <hr>
            {{ trans('instances.boarding.publicKeyLink') }}
            <code>
                {{ $instance->getBoardingUrl() }}

            </code>
            <ul class="nf-actions">
                <li class="nf-action">
                    <a href="{{ url()->route('instance.boarding', ['action' => 'disable-key']) }}" class="nf-btn">
                        <span class="btn-txt">
                            @if($instance->getParameter('boarding_on_key_disable') == null || $instance->getParameter('boarding_on_key_disable') == 0)
                                {{ trans('instances.boarding.createWithKeyDisable') }}
                            @else
                                {{ trans('instances.boarding.createWithKeyEnable') }}
                            @endif
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!--  CONSENT CHARTER -->
    <div class="nf-form nf-col-2">
        <div class="nf-settings-title">
            {{ trans('instances.boarding.consentCharter.title') }}
        </div>
        {{ Form::open(['route' => ['instance.boarding', 'consent-charter']]) }}

        <div class="nf-form-informations">
            <label class="nf-form-box">
                {{ Form::checkbox('consent_charter', '1', ($localConsentState == true) ) }}
                <div class="nf-box-title">{{ trans('instances.boarding.consentCharter.state') }}</div>
            </label>

            <div class="consent_charter_content {{ ($localConsentState != true) ? 'd-none' : '' }}">
                <label class="nf-form-cell nf-cell-full">
                    {{ Form::textarea('consent_charter_content', $localConsentContent, ['rows' => 25, 'id' => 'consent_charter_content']) }}
                    <span class="nf-form-label">
                        {{ trans('instances.boarding.consentCharter.content')}}
                    </span>
                </label>
            </div>
        </div>
        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('form.save') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
    </div>

@stop

@section('javascripts')
@parent
{{ HTML::script('packages/netframe/media/js/instances-medias.js') }}
<script src="https://cdn.tiny.cloud/1/m8doixw5nswt5u98hrazpjb1sw8s2hc90pmz2dogk243jzty/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#consent_charter_content',
    menubar: false,
    toolbar: 'undo redo | formatselect | ' +
        'bold italic | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
    language: '{{ App::getLocale() }}_{{ strtoupper(App::getLocale()) }}'
});

(function () {
    $(document).on('change', 'input[type="checkbox"][name="consent_charter"]', function(e){
        if ($(this).is(':checked')) {
            $('.consent_charter_content').removeClass('d-none');
        }
        else {
            $('.consent_charter_content').addClass('d-none');
        }
    });
})();
</script>
@endsection
