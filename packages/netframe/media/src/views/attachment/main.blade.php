<?php $profile = \SessionHelper::profile('current') ?>

<div class="modal fade" id="{{ isset($modalId) ? $modalId : 'attachMediaModal' }}"  role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ trans('media::messages.attachment') }}</h4>
            </div>

            <div class="modal-body glid-wrap">
                <div class="form-group clearfix">
                    <div class="pull-left">
                        <h5>
                            {{ trans('netframe.publishAs') }} : {{ HTML::publishAs($NetframeProfiles, ['id'=>'id_foreign', 'type'=>'type_foreign', 'postfix'=>'md'], false) }}
                        </h5>
                    </div>
                </div>

                <div id="publish-as-hidden-md">
                    @if (!$profile)
                        {{ Form::hidden('id_foreign') }}
                        {{ Form::hidden('type_foreign') }}
                    @else
                        {{ Form::hidden('id_foreign', $profile->id) }}
                        {{ Form::hidden('type_foreign', $profile->profile) }}
                    @endif
                </div>


                    {{--
                    <div class="panel-heading">
                        <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-upload"></span> {{ trans('media::messages.upload_media') }}</h3>
                    </div>--}}

                        @include('media::attachment.upload',array('autoUpload' => '0'))



                <div class="panel panel-default hidden importMediaModal">
                    <div class="panel-heading">
                        <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-download"></span> {{ trans('media::messages.import_media') }}</h3>
                    </div>
                    <div class="panel-body">
                        @include('media::attachment.import')
                    </div>
                </div>

                <div class="btn-group">
                    <label class="btn btn-default">
                        <input type="radio" name="{{ $modalId }}Confidentiality" value="0" autocomplete="off"> {{ trans('media::messages.private') }}
                    </label>
                    <label class="btn btn-default">
                        <input type="radio" name="{{ $modalId }}Confidentiality" value="1" autocomplete="off" checked="true"> {{ trans('media::messages.public') }}
                    </label>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Template to display a single media -->
<script id="mediaNewsTemplate" type="text/x-handlebars-template">
    <li class="col-sm-2 col-xs-2" data-id="@{{ media.id }}">
            @{{#if_eq media.mediaPlatform "local"}}
                @{{#if_eq media.type "0"}}
                    <img src="/media/download/@{{ media.id }}?thumb=1" class="img-responsive" />
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

            @{{#if_eq media.mediaPlatform "vimeo"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" class="img-responsive" />
            @{{/if_eq}}

            @{{#if_eq media.mediaPlatform "youtube"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" class="img-responsive" />
            @{{/if_eq}}

            @{{#if_eq media.mediaPlatform "dailymotion"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" class="img-responsive" />
            @{{/if_eq}}

            @{{#if_eq media.mediaPlatform "soundcloud"}}
                <img src="/media/download/@{{ media.id }}?thumb=1" class="img-responsive" />
            @{{/if_eq}}
    </li>
</script>
