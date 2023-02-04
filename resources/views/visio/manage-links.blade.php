<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('channels.visio.manageAccess') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body modal-link-visio nf-form nf-col-2">
    {{ Form::open(['class' => 'no-auto-submit fn-add-visio-access']) }}
        {{ Form::hidden('channelId', $channel->id) }}
        <label class="nf-form-cell">
            {{ Form::date('startDate', '', ['class' => 'nf-form-input', 'required']) }}
            <span class="nf-form-label">
                {{ trans('channels.visio.startDate') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell">
            {{ Form::time('startTime', '', ['class' => 'nf-form-input', 'required']) }}
            <span class="nf-form-label">
                &nbsp;
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell">
            {{ Form::date('endDate', '', ['class' => 'nf-form-input', 'required']) }}
            <span class="nf-form-label">
                {{ trans('channels.visio.endDate') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>
        <label class="nf-form-cell">
            {{ Form::time('endTime', '', ['class' => 'nf-form-input', 'required']) }}
            <span class="nf-form-label">
                &nbsp;
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>

        <label class="nf-form-cell nf-cell-full">
            {{ Form::select('timezone', $timeZones, $userTimeZone, ['class' => 'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('channels.visio.external.timezone') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>

        {{--
        <div class="nf-cell-full">
            <a data-toggle="collapse" href="#contactInfos" role="button" aria-expanded="false" aria-controls="contactInfos">
                <strong>>> {{ trans('channels.visio.addContact') }}</strong>
            </a>

            <div class="collapse" id="contactInfos">
                <label class="nf-form-cell">
                    {{ Form::text('firstname', '', ['class' => 'nf-form-input']) }}
                    <span class="nf-form-label">
                        {{ trans('channels.visio.external.firstname') }}
                    </span>
                    <div class="nf-form-cell-fx"></div>
                </label>
                <label class="nf-form-cell">
                    {{ Form::text('lastname', '', ['class' => 'nf-form-input']) }}
                    <span class="nf-form-label">
                        {{ trans('channels.visio.external.lastname') }}
                    </span>
                    <div class="nf-form-cell-fx"></div>
                </label>
                <label class="nf-form-cell nf-cell-full">
                    {{ Form::email('email', '', ['class' => 'nf-form-input']) }}
                    <span class="nf-form-label">
                        {{ trans('channels.visio.external.email') }}
                    </span>
                    <div class="nf-form-cell-fx"></div>
                </label>
            </div>
        </div>
        --}}

        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('channels.visio.add') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}

    <hr>

    <table class="table access-links">
        <thead>
            <tr>
                <th>{{ trans('channels.visio.link') }}</th>
                <th>{{ trans('channels.visio.startDate') }}</th>
                <th>{{ trans('channels.visio.endDate') }}</th>
                <th>{{ trans('channels.visio.external.timezone') }}</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            @foreach($channel->externalAccess as $access)
                @include('visio.link-line', ['access' => $access])
            @endforeach
        </tbody>
    </table>

</div>
<!-- End MODAL-BODY -->

<script>
$(document).on('submit', '.fn-add-visio-access', function(e){
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var _form = $(this);

    var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

    $.ajax({
        url: laroute.route('visio.link.add'),
        data: formData,
        type: "POST",
        success: function( data ) {
            $(data.view).appendTo($('table.access-links tbody')).show().slideDown('normal');
        }
    });
});
</script>