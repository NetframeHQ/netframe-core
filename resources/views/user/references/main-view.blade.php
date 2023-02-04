<li class="help-block clearfix hidden-xs hidden-sm" id="profileReferences">
    <div class="card" id="mini-mosaic-references">
        <div class="card-body">
            <h3 class="text-center">{{ trans('user.references') }}</h3>
            <ul class="list-unstyled" id="userReferenceList">
                @if(count($user->userReferences) > 0)
                    @foreach($user->userReferences as $reference)
                        @include('user.references.unit-reference')
                    @endforeach
                @elseif($rights && $rights < 3)
                    <p class="tl-wording">
                        {{ trans('user.giveReferences') }}
                    </p>
                @endif
            </ul>
            <hr />
            @include('user.references.form')
        </div>
    </div>
</li>

@section('javascripts')
@parent
<script>
(function($){
    $(document).on('click', '.fn-valid-user-ref', function(event){
        event.preventDefault();
        el = $(this);
        referenceId = el.data('reference-id');
        url = $(this).attr('href');

        //send reference id in validator controller return get unit view
        $.post(url, {referenceId: referenceId })
        .success(function (data) {
            elTarget = $(data.targetId);
            console.log(elTarget);
            //modify reference display
            elTarget.fadeOut('slow', function() {
                elTarget.replaceWith(data.viewReplace);
                elTarget.fadeIn('slow');
            });
        });
    });
})(jQuery);
</script>
@stop