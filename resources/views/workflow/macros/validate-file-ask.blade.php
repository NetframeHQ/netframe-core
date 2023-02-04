{{ $media->name }}

@if ($media->type == \Netframe\Media\Model\Media::TYPE_DOCUMENT || $media->type == \Netframe\Media\Model\Media::TYPE_ARCHIVE)
    <a class="btn btn-sm btn-default" href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank">
        <span class="glyphicon glyphicon-download">{{ trans('workflow.notifActions.viewFile') }}</span>
    </a>
@else
    <a class="btn btn-sm btn-default" href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank">
        <span class="glyphicon glyphicon-download">{{ trans('workflow.notifActions.viewFile') }}</span>
    </a>
    {{--
    <a class="viewMedia"
        data-media-name="{{ $media->name }}"
        data-media-id="{{ $media->id }}"
        data-media-type="{{ $media->type }}"
        data-media-platform="{{ $media->platform }}"
        data-media-mime-type="{{ $media->mime_type }}"

        @if ($media->platform !== 'local')
            data-media-file-name="{{ $media->file_name }}"
        @endif
        >
        {!! \HTML::thumbnail($media, 100, 100, array('class' => 'img-thumbnail')) !!}
    </a>
    --}}
@endif

@if($wfAction->action_date != null)
    {{ trans('workflow.notifActions.before') }} {{ date('d/m/Y', strtotime($wfAction->action_date)) }}
@endif


<a class="btn btn-sm btn-success wf-validate-file" data-wf-action-id="{{ $wfAction->id }}" data-validate-status="accept" data-notif-id="{{ $notif->id }}" data-file-id="{{ $media->id }}">
    {{ trans('workflow.notifActions.validate') }}
</a>
<a class="btn btn-sm btn-warning wf-validate-file" data-wf-action-id="{{ $wfAction->id }}" data-validate-status="decline" data-notif-id="{{ $notif->id }}" data-file-id="{{ $media->id }}">
    {{ trans('workflow.notifActions.decline') }}
</a>
<div class="d-none notif-decline-form-{{ $notif->id }}">
{{ Form::textarea('decline_reason', '', ['rows' => 3, 'class' => 'form-group', 'placeholder' => trans('workflow.notifActions.declineReason')]) }}
    <a class="btn btn-sm btn-warning wf-validate-file" data-wf-action-id="{{ $wfAction->id }}" data-validate-status="decline-send" data-notif-id="{{ $notif->id }}" data-file-id="{{ $media->id }}">
        {{ trans('workflow.notifActions.declineSend') }}
    </a>
</div>