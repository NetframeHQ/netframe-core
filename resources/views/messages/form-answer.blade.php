<div class="message-anwser-input">
    {{ Form::open(['url'=> '/messages/message-post', 'id' => 'form-post-message',]) }}
        {{ Form::hidden("receiver_id", (isset($inputOld['receiver_id']) ? $inputOld['receiver_id'] : $idForeignTo) ) }}
        {{ Form::hidden("receiver_type", (isset($inputOld['receiver_type']) ? $inputOld['receiver_type'] : $typeForeignTo) ) }}

        {{ Form::hidden("sender_id", (isset($inputOld['sender_id']) ? $inputOld['sender_id'] : $idForeignFrom) ) }}
        {{ Form::hidden("sender_type", (isset($inputOld['sender_type']) ? $inputOld['sender_type'] : $typeForeignFrom) ) }}

        {{ Form::hidden("overrideType", (isset($inputOld['overrideType']) ? $inputOld['overrideType'] : $overrideType) ) }}
        {{ Form::hidden('feedId',$feedId) }}
        {{ Form::hidden("type", (isset($inputOld['type']) ? $inputOld['type'] : $type) ) }}

        {{ Form::message() }}

        <div class="nf-message-input">
            {{ Form::textarea('content', (isset($inputOld['content']) ? $inputOld['content'] : null), [
                    'rows'=>'2',
                    'id' => 'form-textarea-answer',
                    'placeholder' => trans('messages.answer')
                ] )
            }}
        </div>
        <ul class="nf-actions">
              <!-- SEND -->
              <li class="nf-action">
                <button type="submit" title="Cliquez pour envoyer" class="btn-channel nf-btn btn-ico">
                  <span class="btn-img svgicon">
                    <span class="svgicon fn-submit">
                      @include('macros.svg-icons.enter')
                    </span>
                  </span>
                </button>
              </li>
            </ul>
    {{ Form::close() }}
</div>