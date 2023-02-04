<tr id="access-{{ $access->id }}">
    <td>
        {{ $access->getUrl() }}
        @if($access->firstname != null || $access->lastname != null || $access->email != null)
            <br>
            <strong>{{ trans('channels.visio.contactInfos') }} : </strong>
            {{ $access->firstname }} {{ $access->lastname }}
            @if($access->email != null)
                <a href="mailto:{{ $access->email }}">{{ $access->email }}</a>
            @endif
        @endif
    </td>
    <td class="text-center">
        {{ date('d/m/Y H:i', strtotime(\App\Helpers\DateHelper::convertFromLocalUTC($access->start_at, 'datetime', (($access->timezone != null) ? $access->timezone : null)))) }}
    </td>
    <td class="text-center">
        {{ date('d/m/Y H:i', strtotime(\App\Helpers\DateHelper::convertFromLocalUTC($access->expire_at, 'datetime', (($access->timezone != null) ? $access->timezone : null)))) }}
    </td>
    <td class="text-center">
        @if($access->timezone != null)
            {{ $access->timezone }}
        @else
            &nbsp;
        @endif
    </td>
    <td>
        <a href="{{ url()->route('visio.link.delete', ['channel_id' => $access->channels_id, 'access_id' => $access->id]) }}" class="fn-confirm-delete" data-txtconfirm="{{ trans('channels.visio.confirmDelete') }}">
            <span class="svgicon">
                @include('macros.svg-icons.trash')
            </span>
        </a>
    </td>
</tr>
