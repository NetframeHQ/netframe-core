@if($viewType == 'full')
    <div class="modal-header">
        <h4 class="modal-title">
            {{ trans('page.mediaComments') }}
        </h4>
        <a class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">{{trans('form.close') }}</span>
        </a>
    </div>
    <!-- End MODAL-HEADER -->

    <div class="modal-body" id="comments-Media-{{ $media->id }}">
        @if($linkMoreComments)
            <p class="text-center">
                <a href="{{ url()->route('media.comments', ['mediaId' => $media->id, 'take' => 'all' ]) }}" class="btn btn-default fn-media-all-comments">{{ trans('netframe.moreComments') }}</a>
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
            <a href="{{ url()->to('netframe/form-comment-media', [$media->id]) }}" class="link-netframe btn-sm float-left" data-toggle="modal" data-target="#modal-ajax-comment2">
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
    $(document).on('click','.fn-media-all-comments', function(e){
        e.preventDefault();

        actionUrl = $(this).attr('href');
        btnAction = $(this);
        $.ajax({
            url: actionUrl,
            type: "GET",
            success: function( data ) {
                btnAction.closest('p').hide();
                $("#comments-Media-{{ $media->id }} .block-comment").prepend(data.view);
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });

    });
})(jQuery);
</script>