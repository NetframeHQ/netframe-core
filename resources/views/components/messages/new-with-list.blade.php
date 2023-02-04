<div class="panel clearfix" id="netframeNewMessage">
    <div class="well well-sm">
        {{ Form::open(['url'=> '/messages/message-post', 'id' => 'form-post-page', 'class' => 'fn-inbox-newmail' ]) }}

        {{ Form::hidden("receiver_id", (isset($inputOld['receiver_id']) ? $inputOld['receiver_id'] : '') ) }}
        {{ Form::hidden("receiver_type", (isset($inputOld['receiver_type']) ? $inputOld['receiver_type'] : '') ) }}

        {{ Form::hidden("sender_id", (isset($inputOld['sender_id']) ? $inputOld['sender_id'] : auth()->guard('web')->user()->id) ) }}
        {{ Form::hidden("sender_type", (isset($inputOld['sender_type']) ? $inputOld['sender_type'] : 'user') ) }}

        {{ Form::hidden("overrideType", (isset($inputOld['overrideType']) ? $inputOld['overrideType'] : $overrideType) ) }}

        {{ Form::hidden("type", (isset($inputOld['type']) ? $inputOld['type'] : $type) ) }}

        {{ Form::hidden("from", "inbox" ) }}

        {{ Form::message() }}

        <div class="form-group">
            <div class="row">
                <div class="float-left">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ Form::label('to', trans('messages.to')) }}
                </div>
                <div class="col-md-10 col-xs-10 float-left">
                    {{ Form::hidden("to", "", ['class' => 'fn-list-contact form-control'] ) }}
                </div>
            </div>
        </div>


        <div class="form-group">
            {{ Form::label('content', trans('messages.content'.ucfirst($types[$type+$overrideType]))) }}
            <br />
            {{ Form::textarea( 'content', (isset($inputOld['content']) ? $inputOld['content'] : null), ['rows'=>'7', 'class'=>'form-control'] ) }}
        </div>

        <button type="submit" class="btn btn-border-default float-right">
            {{ trans('form.send') }}
        </button>
        {{ Form::close() }}
    </div>
</div>

@if(!Request::ajax())
    @section('javascripts')
    @parent
@endif
<script>
(function($) {
    var elContact = $(".fn-list-contact");

    $(elContact).select2({
        tags: true,
        data: {!! $contactList !!},
        maximumSelectionSize: 1,
    });

    $(elContact).on("change", function(e) {
        profile = $(elContact).val();
        //split and affect vals to inpouts hidden
        if(profile != ''){
            profileInfos = profile.split("-");
            profileId = profileInfos[1];
            profileType = profileInfos[0];
        }
        else{
            profileId = '';
            profileType = '';
        }

        profileType = profileType.toLowerCase();

        $("input[name=receiver_id]").val(profileId);
        $("input[name=receiver_type]").val(profileType);

     });

    //send form function
    $(document).on('submit', '.fn-inbox-newmail', function(e){
        e.preventDefault();
        _form = $(this);
        formContent = _form.find('textarea[name=content]').val();
        formReceiverId = _form.find('input[name=receiver_id]').val();
        formReceiverType = _form.find('input[name=receiver_type]').val();

        if(formContent == '' || formReceiverId == '' || formReceiverType == ''){
            alertMessage = '';
            if(formReceiverId == '' || formReceiverType == ''){
                alertMessage = alertMessage+'{{ trans('messages.emptyReceiver') }}\n';
            }
            if(formContent == ''){
                alertMessage = alertMessage+'{{ trans('messages.emptyContent') }}\n';
            }
            alert(alertMessage);
        }
        else{
            //submit form in ajax and check return
            var actionUrl = _form.attr('action');
            var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

            // add data to object array serialized json
            formData.push({
                name: "httpReferer",
                value: "{{ Request::url() }}",
                from: "inbox"
            });

            _form.find('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: actionUrl,
                data: formData,
                type: "POST",
                success: function( data ) {
                    window.location = '{{ Request::url() }}';
                },
                error: function(textStatus, errorThrown) {
                    //console.log(textStatus);
                }
            });
        }
    });
})(jQuery);
</script>
@if(!Request::ajax())
    @stop
@endif