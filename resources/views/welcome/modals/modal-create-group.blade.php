<div class="modal-header">
    <h4 class="modal-title text-center mb-2 mb-md-3" id="groupNameTitle">{{ trans('welcome.modals.modal2.title1') }}<br><strong>{{ trans('welcome.modals.modal2.title2') }}</strong></h4>
    <div class="text-center">
        @include('macros.svg-icons.community_big')
    </div>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    {{ Form::open(['route' => 'community.edit', 'class' => 'box form-community']) }}
        {{ Form::hidden("id_foreign", auth()->guard('web')->user()->id ) }}
        {{ Form::hidden("type_foreign", 'user' ) }}
        {{ Form::hidden('confidentiality', '1') }}
        {{ Form::hidden('free_join', '0') }}
        <div class="form-group mb-3">
            {{ Form::label('name', trans('welcome.modals.modal2.grpName')) }}
            {{ Form::text('name', (isset($community->id) ? $community->name : \App\Helpers\InputHelper::get('name')), ['class'=>'form-control', 'placeholder' => trans('welcome.modals.modal2.grpPlaceholder')] ) }}
            {!! $errors->first('name', '<p class="input__error mb-0 mt-2 ft-600"><img src="'. asset('assets/img/boarding/alert-circle.svg') .'" alt="icon erreur" class="is-inline" /> :message</p>') !!}
        </div>
        <!-- Error example -->
        <!-- <div class="form-group mb-3">
            <label for="gname">Nom du groupe de travail</label>
            <input type="text" class="form-control is-invalid" id="gname" placeholder="ex : Équipe de vente - Paris">
            <div class="invalid-feedback">Error message</div>
        </div> -->
        <div class="form-group mb-3">
            {{ Form::label('description', trans('welcome.modals.modal2.descName')) }}
            {{ Form::textarea('description', '', ['rows'=>'5', 'class'=>'form-control', 'placeholder' => trans('welcome.modals.modal2.descPlaceholder')] ) }}
            {!! $errors->first('description', '<p class="invalid-feedback">:message</p>') !!}
        </div>

        @include('components.forms.tags', ['element' => $community, 'noBr' => true])

        <button class="btn btn-primary btn-block" type="submit" >{{ trans('boarding2020.submit') }}</button>
    {{ Form::close() }}

    <p class="text-center mt-4 mb-0"><a href="{{ url()->route('welcome.modal.invite.users') }}" class="text-white boarding-load-modal">{{ trans('boarding2020.skip') }}</a></p>
</div>


<script>
(function($){
    $('.fn-select2-tag').select2({
        language: "{{ Lang::locale() }}",
        minimumInputLength: 2,
        multiple: true,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: "{{ URL::route('tags.autocomplete') }}",
            dataType: 'json',
            contentType: "application/json",
            type: "POST",
            data: function (params) {
                return  JSON.stringify({
                    q: params.term
                });
            },
            processResults: function (data, page) {
                return data;
            },
        },
        escapeMarkup: function (markup) { return markup; },
    });
})(jQuery);
</script>
