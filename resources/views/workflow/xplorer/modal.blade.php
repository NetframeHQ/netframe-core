<div class="workflow-container" data-profile-id="{{$profile_id}}" data-profile-type="{{$profile_type}}">
    <label class="checkbox-slider">
        {{ trans('workflow.makeWorkflow') }}&nbsp;
        {{ Form::checkbox('makeWorkflow', '1', (isset($forceWorkflow) && $forceWorkflow) ? true : false, ['class' => 'fn-active-workflow']) }}
        <span></span>
    </label>
    @include('workflow.make-container')
</div>

<script>
(function($){
    // load workflow by default
    if($('input.fn-active-workflow').is(':checked')) {
        $('input.fn-active-workflow').trigger('change');
    }
})(jQuery);

</script>