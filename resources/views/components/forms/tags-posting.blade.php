<div class="panel-post-tags">
    <span class="tags-icon">
        <span class="svgicon">
            @include('macros.svg-icons.tags')
        </span>
    </span>
    {{ Form::select('tags[]',
        (isset($element->formTags)) ? $element->formTags : $element->tagsList(),
        (isset($element->formTagsSelecteds)) ? $element->formTagsSelecteds : $element->tagsList(true),
        [
            'id' => 'tags',
            'class' => 'form-control fn-select2-tag',
            'multiple' => 'multiple',
            'style' => 'width:auto',
            'placeholder' => trans('tags.addTags')
        ]) }}
</div>




@section('javascripts')
    @parent
<script>
(function($){
    $('.fn-select2-tag').select2({
        dropdownCssClass : 'bigdrop',
        placeholder: function(){
            $(this).data('placeholder');
        },
        allowClear: true,
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
    $('.fn-select2-tag').find('ul.select2-selection__rendered').addClass('tags-list');
})(jQuery);
</script>
@stop