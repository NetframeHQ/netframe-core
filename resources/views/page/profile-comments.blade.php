@if($viewType == 'full')
    <div class="modal-header">
        <h4 class="modal-title">
            <span class="glyphicon glyphicon-comment"></span> {{ trans('page.profileComments') }}
        </h4>
        <a class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">{{trans('form.close') }}</span>
        </a>
    </div>
    <!-- End MODAL-HEADER -->

    <div class="modal-body" id="comments-{{ studly_case($profile->getType()) }}-{{ $profile->id }}">
        @if($linkMoreComments)
            <p class="text-center">
                <a href="{{ url()->route('profile.comments', ['profileType' => $profile->getType(), 'profileId' => $profile->id, 'take' => 'all' ]) }}" class="btn btn-default fn-profile-all-comments">{{ trans('netframe.moreComments') }}</a>
            </p>
        @endif
        <div class="block-comment clearfix">
@endif

@foreach($comments as $comment)
    @include('page.comment')
@endforeach

@if($viewType == 'full')
            </div>

        <div class="area-comment-toolbar clearfix">
            <a href="{{ url()->to('netframe/form-comment-profile', [$profile->getType(), $profile->id]) }}" class="link-netframe btn-sm float-left" data-toggle="modal" data-target="#modal-ajax-comment">
                {{ trans('netframe.comment') }}
            </a>
        </div>
    </div>
    <!-- End MODAL-BODY -->

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('form.close') }}</button>
    </div>
@endif

<script>
(function($){
    $(document).on('click','.fn-profile-all-comments', function(e){
        e.preventDefault();

        actionUrl = $(this).attr('href');
        btnAction = $(this);
        $.ajax({
            url: actionUrl,
            type: "GET",
            success: function( data ) {
                btnAction.closest('p').hide();
                $("#comments-{{ studly_case($profile->getType()) }}-{{ $profile->id }} .block-comment").prepend(data.view);
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });

    });
})(jQuery);
</script>