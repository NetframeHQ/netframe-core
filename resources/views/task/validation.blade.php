@extends('task.layout')

@section('subcontent')
<div class="main-container no-side">
    <div id="container-tasks" class="nf-table-container">
        <table class="table nf-table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>{{ trans('task.validations.file') }}</th>
                    <th>{{ trans('task.validations.action') }}</th>
                    <th>{{ trans('task.validations.status') }}</th>
                    <th>{{ trans('task.validations.userInfos') }}</th>
                    <th>{{ trans('task.validations.delay') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                {{-- main and sub tasks --}}
                @foreach($workflows as $workflow)
                    @php
                        $media = $workflow->media();
                    @endphp
                    @if($media != null)
                        <tbody id="wf-{{ $workflow->id }}">
                            <tr class="task">
                                <td>
                                    @if($media->isDocument())
                                        @php
                                        if ($media->isDocument() && !$activeOffice && $media->feed_path != null) {
                                            $link = url()->route('media.pdf.viewer').'?file='.urlencode(URL::route('media_download', ['id' => $media->id, 'feed' => true]));
                                            $download = false;

                                        /* Ouvre un PDF avec la visionneuse */
                                        } elseif ("application/pdf" === $media->mime_type) {
                                            $link = url()->route('media.pdf.viewer').'?file='.URL::route('media_download', ['id' => $media->id]);
                                            $download = false;

                                        /* Télécharge le document */
                                        }else {
                                            $link = url()->route('media_download', array('id' => $media->id));
                                            $download = true;
                                        }
                                        @endphp
                                        <a href="{{ $link }}" target="_blank" class="item-link" @if($download) download @endif>
                                    @elseif (!$media->isTypeDisplay())
                                        <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="item-link" download>
                                    @else
                                        <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="item-link" download>
                                        {{--
                                        <a class="viewMedia item-link"
                                            data-media-name="{{ $media->name }}"
                                            data-media-id="{{ $media->id }}"
                                            data-media-type="{{ $media->type }}"
                                            data-media-platform="{{ $media->platform }}"
                                            data-media-mime-type="{{ $media->mime_type }}"
                                            href="#"

                                            @if ($media->platform !== 'local')
                                                data-media-file-name="{{ $media->file_name }}"
                                            @endif
                                        >
                                        --}}
                                    @endif
                                        {!! HTML::thumbnail($media, '', '', []) !!}
                                    </a>
                                    {{-- add thumb and download link --}}
                                    &nbsp;
                                </td>
                                <td>
                                    <div class="nf-task-cell">
                                        @if($media->isDocument())
                                            @php
                                            if ($media->isDocument() && !$activeOffice && $media->feed_path != null) {
                                                $link = url()->route('media.pdf.viewer').'?file='.urlencode(URL::route('media_download', ['id' => $media->id, 'feed' => true]));
                                                $download = false;

                                            /* Ouvre un PDF avec la visionneuse */
                                            } elseif ("application/pdf" === $media->mime_type) {
                                                $link = url()->route('media.pdf.viewer').'?file='.URL::route('media_download', ['id' => $media->id]);
                                                $download = false;

                                            /* Télécharge le document */
                                            }else {
                                                $link = url()->route('media_download', array('id' => $media->id));
                                                $download = true;
                                            }
                                            @endphp
                                            <a href="{{ $link }}" target="_blank" class="item-link" @if($download) download @endif>
                                        @elseif (!$media->isTypeDisplay())
                                            <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="item-link" download>
                                        @else
                                            <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="item-link" download>
                                            {{--
                                            <a class="viewMedia item-link"
                                                data-media-name="{{ $media->name }}"
                                                data-media-id="{{ $media->id }}"
                                                data-media-type="{{ $media->type }}"
                                                data-media-platform="{{ $media->platform }}"
                                                data-media-mime-type="{{ $media->mime_type }}"
                                                href="#"

                                                @if ($media->platform !== 'local')
                                                    data-media-file-name="{{ $media->file_name }}"
                                                @endif
                                            >
                                            --}}
                                        @endif
                                        <strong>{{ $media->name }}</strong>
                                        </a>
                                    </div>
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>
                                    <a href="{{ url()->route('wf.delete', ['id' => $workflow->id]) }}"  class="statut bg-light fn-confirm-delete" data-txtconfirm="{{ trans('workflow.delete.text') }}">
                                        {{ trans('workflow.delete.link') }}
                                    </a>
                                </td>
                            </tr>
                            @foreach($workflow->detailsActions as $action)
                                <tr class="task sub">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>
                                        {{trans('workflow.actions.' . $action->actions->action_type)}}
                                    </td>
                                    <td>
                                        @if($action->action_validate)
                                            <span class="statut alert-success">{{trans('task.state.complete')}}</span>
                                        @else
                                            <span class="statut alert-danger">{{trans('task.state.todo')}}</span>
                                        @endif
                                    </td>
                                    <td class="user-col field">
                                        @if($action->users_id != null)
                                            <div class="nf-task-cell">
                                                @if($action->user->profileImage != null)
                                                    {!! HTML::thumbImage($action->user->profileImage, 80, 80, [], $action->user->getType(), 'avatar') !!}
                                                @else
                                                    <span class="svgicon">
                                                      @include('macros.svg-icons.user')
                                                    </span>
                                                @endif
                                                {{ $action->user->getNameDisplay() }}
                                            </div>
                                        @endif

                                        @if ($action->actions->action_type == 'destination_folder')
                                            @if($action->destinationFolderProfile() != null)
                                                <div class="nf-task-cell">
                                                    @if($action->destinationFolderProfile()->profileImage != null)
                                                        {!! HTML::thumbImage($action->destinationFolderProfile()->profileImage, 80, 80, [], $action->destinationFolderProfile()->getType(), 'avatar') !!}
                                                    @else
                                                        <span class="svgicon">
                                                          @include('macros.svg-icons.' . $action->destinationFolderProfile()->getType())
                                                        </span>
                                                    @endif
                                                    {{ $action->destinationFolderProfile()->getNameDisplay() }}
                                                    <span class="svgicon">
                                                        @include('macros.svg-icons.arrow-right')
                                                    </span>
                                                    <span class="svgicon">
                                                        @include('macros.svg-icons.doc')
                                                    </span>
                                                    {{ $action->destinationFolder()->getNameDisplay() }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($action->action_date != null)
                                            @if($action->action_date < date('Y-m-d'))
                                                <span class="statut alert-danger">
                                            @elseif($action->action_date > date('Y-m-d') && (((strtotime($action->action_date) - time()) / (60*60*24)) < 5 ))
                                                <span class="statut alert-warning">
                                            @else
                                                <span class="statut alert-info">
                                            @endif
                                                {{ date('d/m/Y', strtotime($action->action_date)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$action->action_validate && $action->users_id != null)
                                            <a href="#" data-action="{{$action->id}}" class="statut bg-light fn-revive-validation">{{trans('task.revive')}}</a>
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function () {
    var $modal = $('#viewMediaModal');
    var baseUrl = '{{ url()->to('/') }}';

    audiojs.events.ready(function() {
        audiojs.createAll();
    });

    new PlayMediaModal({
        $modal: $modal,
        $modalTitle: $modal.find('.modal-title'),
        $modalContent: $modal.find('.modal-carousel .carousel-item'),
        $media: $('.viewMedia'),
        $directoryMode: true,
        baseUrl: baseUrl
    });
})
</script>
@endsection
