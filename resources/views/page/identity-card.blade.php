<div class="modal-header vpadding-5">
    <h4>
        <a href="{{ $profile->getUrl() }}">
            {{ $profile->getNameDisplay() }} {!! HTML::online($profile, true) !!}
        </a>
        @if($profile->location != null)
            <small>- {{ $profile->location }}</small>
        @endif
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<div class="modal-body">
    <div class="fn-swipeMosaic panel-default clearfix">
        <div class="panel-body col-md-12 col-xs-12">
            <div class="row">

                @if($prevId != 0)
                    <div class="card-arrow-nav card-left">
                        <a id="lastProfile" class="btn btn-netframe btn-arrow-card" data-profile-id="{{ $prevId }}" data-profile-type="{{ $prevProfile }}" data-toggle="tooltip" title="{{ trans('netframe.last') }}">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                        </a>
                    </div>
                @endif

                <div class="col-sm-1 visible-sm">
                </div>
                <div class="col-md-4 offset-md-1 col-xs-6 col-sm-4">
                    <div class="text-center mg-bottom-10">
                        @if($profilePicture != null)
                            <a href="" class="viewMedia"
                                data-media-name="{{ $profilePicture->name }}"
                                data-media-id="{{ $profilePicture->id }}"
                                data-media-type="{{ $profilePicture->type }}"
                                data-media-platform="{{ $profilePicture->platform }}"
                                data-media-mime-type="{{ $profilePicture->mime_type }}"

                                @if ('local' !== $profilePicture->platform)
                                    data-media-file-name="{{ $fprofilePicture->file_name }}"
                                @endif
                                >
                        @endif
                        {!! HTML::thumbnail($profilePicture, '', '', array('class' => 'img-thumbnail img-fluid'),asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
                        @if($profilePicture != null)
                            </a>
                        @endif
                    </div>
                </div>

                @if($profile->getType() == "user")
                    <div class="col-md-7">
                        @if($profile->phone != '')
                            {{ trans('user.phone') }} : {{ \App\Helpers\StringHelper::formatPhoneNumber($profile->phone) }}
                        @endif
                    </div>
                @else
                    <div class="col-md-7">
                        @if(in_array(class_basename($profile), config('netframe.model_taggables')))
                            @include('tags.element-display', ['tags' => $profile->tags])
                        @endif
                    </div>
                @endif

                @if($nextId != 0)
                    <div class="card-arrow-nav card-right">
                        <a id="nextProfile" class="btn btn-netframe btn-arrow-card" data-profile-id="{{ $nextId }}" data-profile-type="{{ $nextProfile }}" data-toggle="tooltip" title="{{ trans('netframe.next') }}">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                        </a>
                    </div>
                @endif

            </div>

            <div class="row mg-top-10">
                <div class="col-md-12 col-xs-12">
                    <ul class="list-unstyled">
                        @if($profile->description != '')
                            <li>{!! \App\Helpers\StringHelper::collapsePostText($profile->description) !!}</li>
                        @endif

                        @if($profile->getType() == 'user' && $profile->training != '')
                            <li>{!! \App\Helpers\StringHelper::collapsePostText($profile->training) !!}</li>
                        @endif

                        @if($profile->getType() != 'user')
                            <li>
                                <strong>{{ trans('page.createdBy') }} :</strong> <a href="{{ $profile->owner->getUrl() }}"><span class="icon ticon-{{ $profile->owner->getType() }}"></span> {{ $profile->owner->getNameDisplay() }}</a>
                            </li>
                        @endif

                        {{--
                        @if( !empty($profile->location) )
                            <li>
                                <strong>{{ trans('netframe.location') }} : </strong>
                                {{ $profile->location }}
                            </li>
                        @endif
                        --}}
                    </ul>
                    <div class="text-center mg-bottom">
                        <a  class="btn" href="{{ $profile->getUrl() }}">
                            {{ trans('netframe.viewProfile') }}
                        </a>
                    </div>

                    <div class="text-center">
                        {!! HTML::likeBtnProfile($profile, $followed, $liked, $profile->like, $profile->followers()->count()) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var baseUrl = '{{ url()->to('/') }}';

    // view modal media
    var $modal = $('#viewMediaModal');

    new PlayMediaModal({
        $modal: $modal,
        $modalTitle: $modal.find('.modal-title'),
        $modalContent: $modal.find('.modal-body'),
        $media: $('.viewMedia'),
        baseUrl: baseUrl
    });

    lastProfileId = $("#lastProfile").attr('data-profile-id');
    lastProfileType = $("#lastProfile").attr('data-profile-type');
    if(lastProfileId != 0){
        lastUrl = $("#"+lastProfileType+"_"+lastProfileId).attr('href');
        $.ajax({
            url: lastUrl,
            type: "GET",
            success: function( data ) {
                lastProfileIdentity = data;
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }});
    }

    nextProfileId = $("#nextProfile").attr('data-profile-id');
    nextProfileType = $("#nextProfile").attr('data-profile-type');
    if(nextProfileId != 0){
        nextUrl = $("#"+nextProfileType+"_"+nextProfileId).attr('href');
        $.ajax({
            url: nextUrl,
            type: "GET",
            success: function( data ) {
                nextProfileIdentity = data;
            }});
    }

    $("#lastProfile").on("click", lastProfile);
    $("#nextProfile").on("click", nextProfile);

    function showNext(nextId, nextType){
        //get next url by id
        nextUrl = $("#"+nextType+"_"+nextId).attr('href');
        var modalId = '#modal-ajax';
        $(modalId).find('.modal-content').html(nextProfileIdentity);
    }

    function nextProfile(event){
        parentModal = $(".fn-swipeMosaic").closest("#modal-ajax");
        nextProfileId = $("#nextProfile").attr('data-profile-id');
        nextProfileType = $("#nextProfile").attr('data-profile-type');
        if(nextProfileId != 0 && nextProfileId != 'undefined'){
            $(".fn-swipeMosaic").hide("slide", { direction: "left" }, function(){
                $(".fn-swipeMosaic").html('');
                showNext(nextProfileId, nextProfileType);
                $(".fn-swipeMosaic").show();
                });
        }

    }

    function showLast(lastId, lastType){
        //get next url by id
        lastUrl = $("#"+lastType+"_"+lastId).attr('href');
        var modalId = '#modal-ajax';
        $(modalId).find('.modal-content').html(lastProfileIdentity);
    }

    function lastProfile(event){
        parentModal = $(".fn-swipeMosaic").closest("#modal-ajax");
        lastProfileId = $("#lastProfile").attr('data-profile-id');
        lastProfileType = $("#lastProfile").attr('data-profile-type');
        if(lastProfileId != 0 && lastProfileId != 'undefined'){
            $(".fn-swipeMosaic").hide("slide", { direction: "right" }, function(){
                $(".fn-swipeMosaic").html('');
                showLast(lastProfileId, lastProfileType);
                $(".fn-swipeMosaic").show();
                });
        }
    }

    $(document).keydown(function(e) {
        if (e.keyCode == '37') {
           lastProfile();
           e.preventDefault();
        }
        else if (e.keyCode == '39') {
           nextProfile();
           e.preventDefault();
        }
    });
});
</script>

@include('pwkStats')
