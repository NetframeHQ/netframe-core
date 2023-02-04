{{ Form::open(['url'=> '/messages/message-post', 'id' => 'form-post-page']) }}

{{ Form::hidden("receiver_id", (isset($inputOld['receiver_id']) ? $inputOld['receiver_id'] : $idForeignTo) ) }}
{{ Form::hidden("receiver_type", (isset($inputOld['receiver_type']) ? $inputOld['receiver_type'] : $typeForeignTo) ) }}

{{ Form::hidden("sender_id", (isset($inputOld['sender_id']) ? $inputOld['sender_id'] : $idForeignFrom) ) }}
{{ Form::hidden("sender_type", (isset($inputOld['sender_type']) ? $inputOld['sender_type'] : $typeForeignFrom) ) }}

{{ Form::hidden("overrideType", (isset($inputOld['overrideType']) ? $inputOld['overrideType'] : $overrideType) ) }}

{{ Form::hidden("offerId", (isset($inputOld['offerId']) ? $inputOld['offerId'] : $offerId) ) }}
{{ Form::hidden("type", (isset($inputOld['type']) ? $inputOld['type'] : $type) ) }}

{{ Form::message() }}

<label class="nf-form-cell nf-cell-full @if($errors->has('password')) nf-cell-error @endif">
    {{ Form::textarea( 'content', (isset($inputOld['content']) ? $inputOld['content'] : null), ['rows'=>'7', 'class'=>'nf-form-input'] ) }}
    <span class="nf-form-label">
        {{ trans('messages.content'.ucfirst($types[$type+$overrideType])) }}
    </span>
    {!! $errors->first('content', '<p class="invalid-feedback">:message</p>') !!}
    <div class="nf-form-cell-fx"></div>
</label>

