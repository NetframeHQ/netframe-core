{{ Form::open( ['route' => 'playlist_create', 'id' => 'addReferenceForm',
    'name' => 'addReference', 'class' => 'no-auto-submit']) }}

<div class="">
    <div class="col-md-8 form-group">
        {{ Form::text('name', null, ['class' => 'form-control '.(($errors->has('name')) ? 'has-error' : '')]) }}

        @if ($errors->has('name'))
            <span class="invalid-feedback">{{ $errors->first('name') }}</span>
        @endif
    </div>

    <div class="col-md-4">
        <button type="submit" id="addReferenceFormButton" class="btn btn-primary">
            {{ trans('angel.addRef') }}
        </button>
    </div>
</div>
{{ Form::close() }}

@section('javascripts')
@parent
<script>
(function($){
    $(document).on('submit', '#addReferenceForm', function(event){
        event.preventDefault();
        _form = $(this).closest('form');
        inputReference = _form.find('input[name=name]');
        newReference = inputReference.val();

        //send new reference in ajax and get return clear form
        $.post('{{ url()->to('/') }}' + laroute.route('user.reference.add'), {userId:{{$user->id}},newReference: newReference })
        .success(function (data) {
            elTarget = '#userReferenceList';
            inputReference.val('');

            //alert when already exiusts
            if(typeof data.exists != 'undefined') {
                alert('{{ trans('user.refExists') }}');
            }

            if($(elTarget).children('.tl-wording').length > 0){
                $('.tl-wording').remove();
            }

            //display new reference
            if(typeof data.view != 'undefined') {
                $(data.view).appendTo($(elTarget)).show().slideDown('normal');
            }
        });
    });
})(jQuery);
</script>
@stop