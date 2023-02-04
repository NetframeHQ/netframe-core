@foreach($messagesGroups as $group)
    @if($group->lastMessages != null)
        <div class="card inbox{{ ucfirst($types[$group->type]) }} fn-load-message-feed" data-group="{{ $group->lastMessages->messages_mail_group_id }}" data-time="{{ $group->updated_at }}">
            <div class="card-body">
                {!! HTML::thumbImage(
                        $group->profile_foreign->profileImage,
                        30,
                        30,
                        [],
                        $group->profile_foreign->getType(),
                        'avatar ' . $group->profile_foreign->getType(),
                        $group->profile_foreign
                    ) !!}
                @if($group->lastMessages->read == 0 && $group->lastMessages->sender_type.$group->lastMessages->sender_id != get_class($group->profile_to).$group->profile_to->id)
                    <span class="badge-message">{{ $group->unread }}</span>
                @endif

                <time class="datetime float-right">
                    {{ \App\Helpers\DateHelper::messageDate($group->lastMessages->created_at) }}
                </time>
                <strong>{{ $group->profile_foreign->getNameDisplay() }}</strong><br>
                @if(class_basename($group->profile_to) != 'User')
                    <span class="for">
                        {{ trans('messages.messagesTo') }} {{ $group->profile_to->getNameDisplay() }}
                    </span>
                @endif
                <span>
                </span>
            </div>
        </div>
    @endif
@endforeach
