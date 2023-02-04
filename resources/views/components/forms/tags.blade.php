<label class="nf-form-cell nf-cell-full tags-add">
    {{ Form::select('tags[]',
        (isset($element->formTags)) ? $element->formTags : $element->tagsList(),
        (isset($element->formTagsSelecteds)) ? $element->formTagsSelecteds : $element->tagsList(true),
        [
            'id' => 'tags',
            'class' => 'fn-select2-tag',
            'multiple' => 'multiple',
            'style' => 'width:auto',
        ]) }}
    <span class="nf-form-label">
        {{ trans('tags.addTags') }}
    </span>
    <div class="nf-form-cell-fx"></div>
</label>
@if(!isset($noBr))
    <br />
@endif




@section('javascripts')
    @parent
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
@stop