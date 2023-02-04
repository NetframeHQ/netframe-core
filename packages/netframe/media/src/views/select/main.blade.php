<?php $modalId = isset($modalId) ? $modalId : 'selectMediaModal' ?>

<!-- The modal content -->
<div class="modal fade" id="{{ $modalId }}" data-backdrop="static">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select medias</h4>
            </div>

            <div class="modal-body">
                <div id="content-wrapper" class="row"></div>
                <div id="pager-wrapper" class="row text-center"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('media::messages.close') }}</button>
            </div>

        </div>
    </div>
</div>

<!-- Template to display a single media -->
<script id="mediaTemplate" type="text/x-handlebars-template">
    <div class="col-sm-3">
        <div class="selectThumb" data-id="@{{ media.id }}">
            @{{#if_eq media.platform "local"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" style="max-width: 110px; max-height: 110px;" class="img-thumbnail img-responsive profile-image" />
                <span class="selectOverlay">
                <strong>
                        @{{#if_eq media.type "0"}}
                            <span class="glyphicon glyphicon-picture"></span>
                        @{{/if_eq}}

                        @{{#if_eq media.type "1"}}
                            <span class="glyphicon glyphicon-facetime-video"></span>
                        @{{/if_eq}}

                        @{{#if_eq media.type "2"}}
                            <span class="glyphicon glyphicon-headphones"></span>
                        @{{/if_eq}}

                        @{{#if_eq media.type "3"}}
                            <span class="glyphicon glyphicon-file"></span>
                        @{{/if_eq}}

                        @{{#if_eq media.type "4"}}
                            <span class="glyphicon glyphicon-folder-close"></span>
                        @{{/if_eq}}
                </strong>
                </span>
            @{{/if_eq}}

            @{{#if_eq media.platform "vimeo"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" style="max-width: 110px; max-height: 110px;" class="img-responsive profile-image" />
                <span class="selectOverlay">
                <strong><span class="glyphicon glyphicon-facetime-video"></span></strong>
                </span>
            @{{/if_eq}}

            @{{#if_eq media.platform "youtube"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" style="max-width: 110px; max-height: 110px;" class="img-responsive profile-image" />
                <span class="selectOverlay">
                <strong><span class="glyphicon glyphicon-facetime-video"></span></strong>
                </span>
            @{{/if_eq}}

            @{{#if_eq media.platform "dailymotion"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" style="max-width: 110px; max-height: 110px;" class="img-responsive profile-image" />
                <span class="selectOverlay">
                <strong><span class="glyphicon glyphicon-facetime-video"></span></strong>
                </span>
            @{{/if_eq}}

            @{{#if_eq media.mediaPlatform "soundcloud"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" style="max-width: 110px; max-height: 110px;" class="img-responsive profile-image" />
            @{{/if_eq}}
        </div>
    </div>
</script>
