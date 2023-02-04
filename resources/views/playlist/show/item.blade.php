<?php
if($playlistItem->medias_id != null && $playlistItem->medias_id != 0){
    $media = \App\Media::find($playlistItem->medias_id);
}
else{
    $media = $playlistItem->profile->getFavoriteOrLastMedia();
}
?>

@if($media !== null && $media->active == 1)
    <div id="playlist-item-{{ $playlistItem->id }}" class="panel panel-default playlistItem no-padding-border"
        data-media-id="{{ $media->id }}"
        data-media-title="{{ $media->name }}"
        data-media-type="{{ $media->type }}"
        data-media-platform="{{ $media->platform }}"
        data-media-mime-type="{{ $media->mime_type }}"

        @if ($media->platform !== 'local')
            data-media-file-name="{{ $media->file_name }}"
        @endif
    >
@else
    <div id="playlist-item-{{ $playlistItem->id }}" class="panel panel-default playlistItem no-padding-border">
@endif
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-4 col-md-4">
            @if($media !== null && $media->active == 1)
                @if (!$media->isTypeDisplay())
                    <a class="hover-play-none" href="{{ url()->route('media_download', array('id' => $media->id)) }}">
                @else
                    <a href="" class="viewMedia hover-play"
                        data-media-name="{{ $media->name }}"
                        data-media-id="{{ $media->id }}"
                        data-media-type="{{ $media->type }}"
                        data-media-platform="{{ $media->platform }}"
                        data-media-mime-type="{{ $media->mime_type }}"

                        @if ($media->platform !== 'local')
                            data-media-file-name="{{ $media->file_name }}"
                        @endif
                        >
                @endif
                    {!! HTML::thumbnail($media, '', '', array('class' => 'img-fluid img-thumbnail'),asset('assets/img/no-media.jpg')) !!}
                    @if($media->type == \App\Media::TYPE_VIDEO || $media->type == \App\Media::TYPE_AUDIO)
                        <div class="display-play smallTicon">
                            <span class="icon ticon-play"></span>
                        </div>
                    @else
                        <div class="display-play smallTicon">
                            <span class="icon ticon-search"></span>
                        </div>
                    @endif
                </a>
            @else
                <img src='/assets/img/no-media.jpg' class='img-fluid profile-image' />
            @endif
            </div>
            <div class="col-xs-5 col-md-5">
                <div class="row">
                    <ul class="list-unstyled">
                        <li>
                            @if($media != null)
                                @if($media->language != null)
                                <h2 class="title3">{{ trans('page.language') }} : {{ $media->languages->name }}</h2>
                                @endif
                                <small>
                                    <time datetime="{{ $media->date }}">
                                        {{ date("d / m / Y", strtotime($media->date)) }}
                                    </time>
                                </small>
                                <p>{{ $media->description }}</p>
                            @endif
                        </li>
                        @if($playlist->users_id == auth()->guard('web')->user()->id)
                        <li class="mg-top clearfix">
                            @if (count($otherPlaylists) > 0)
                            <div class="dropdown float-left">
                                <button class="btn btn-sm btn-border-default dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{ trans('playlist.add_to') }} <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($otherPlaylists as $otherPlaylist)
                                        <li>
                                            <a class="addToPlaylist" href="{{ url()->route('playlist_item_add', array('id' => $otherPlaylist->id, 'itemId' => $playlistItem->id)) }}">
                                            {{ $otherPlaylist->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <a id="testDeletePlaylist" href="{{ url()->route('playlist_item_delete', ['id' => $playlistItem->id]) }}"
                                class="float-right link-netframe fn-confirm-delete fn-ajax-delete" data-txtconfirm="{{ trans('netframe.confirmDel') }}"
                                title="{{ trans('playlist.delete') }}">
                                    <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-xs-3 col-md-3">
                <a href="{{ $playlistItem->profile->getUrl() }}">
                    <div class="mosaic-content">
                        {!! HTML::thumbnail($playlistItem->profile->profileImage, '', '', ['class' => 'img-fluid'], asset('assets/img/avatar/'.$playlistItem->profile->getType().'.jpg')) !!}
                    </div>
                    <p class="playlist-profile-name"><span class="cut-length-80">{{ ucfirst($playlistItem->profile->getNameDisplay()) }}</span> {!! HTML::online($playlistItem->profile, true) !!}</p>
                </a>
            </div>
        </div>
    </div>
</div>