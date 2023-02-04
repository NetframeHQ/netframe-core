@section('modals')
    <div class="modal fade" id="autoFireMediaModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">&nbsp;</h4>
                <a class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{trans('form.close') }}</span>
                </a>
            </div>
            <!-- End MODAL-HEADER -->

            <div class="modal-body">
                @include('page.type-content.medias.full-size')
            </div>
            <!-- End MODAL-BODY -->

            <div class="modal-footer fn-social-media">
                @include('media.social')
            </div>
            {{ Form::close() }}
            <!-- End MODAL-FOOTER -->
        </div>
    </div>
    </div>
@stop

@if($media->description != null)
    @section('ogdescription')
        :: {{ \App\Helpers\StringHelper::formatMetaText($media->description) }}
    @append
@endif

@section('ogimage')
    <meta property="og:image" content="{{ $media->getUrl() }}">
@overwrite
