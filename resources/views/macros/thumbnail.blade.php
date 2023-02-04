@if ($media)
    @if ($media->platform == 'local')
        @if ($media->type == \App\Media::TYPE_IMAGE)
            @if ($media->thumb_path)
                <div class="nf-thumbnail {{ $profileType }}" style="background-image:url({{ url()->route('media_download', array('id' => $media->id, 'feed' => 1)).'&v='.$media->updated_at->format('U') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
            @else
                <div class="nf-thumbnail {{ $profileType }}" style="background-image:url({{ url()->route('media_download', array('id' => $media->id)).'?v='.$media->updated_at->format('U') }})"
                    {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
            @endif

        @elseif ($media->type == \App\Media::TYPE_VIDEO)
            @if ($media->thumb_path)
                <div class="nf-thumbnail" style="background-image:url({{ url()->route('media_download', array('id' => $media->id, 'feed' => 1)) }})"
                    {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
            @else
                <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Video' ?>
                <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/video.png') }})"
                    {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
            @endif

        @elseif ($media->type == \App\Media::TYPE_AUDIO)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Audio' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/audio.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @elseif ($media->type == \App\Media::TYPE_DOCUMENT)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Document' ?>
            <div class="nf-thumbnail @if($media->thumb_path!=null or $media->thumb_path != '')@else nf-thumbnail-placeholder @endif" style="background-image:url(@if($media->thumb_path!=null or $media->thumb_path != ''){{ url()->route('media_download', ['id' => $media->id, 'feed' => 1]) }} @else {{ \asset('assets/img/icons/file.png') }} @endif"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @elseif ($media->type == \App\Media::TYPE_ARCHIVE)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Archive' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/file.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @elseif ($media->type == \App\Media::TYPE_APPLICATION)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Document' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/file.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @elseif ($media->type == \App\Media::TYPE_SCRIPT)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Document' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/file.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @elseif ($media->type == \App\Media::TYPE_OTHER)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Document' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/file.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @elseif ($media->type == \App\Media::TYPE_FONT)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Document' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/file.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>

        @endif
    @elseif ($media->platform == 'soundcloud' || $media->platform == 'youtube' || $media->platform == 'dailymotion' || $media->platform == 'vimeo')
        <div class="nf-thumbnail" style="background-image:url({{ url()->route('media_download', array('id' => $media->id, 'feed' => 1)) }})"
               {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
    @elseif ($media->platform == 'google_drive' || $media->platform == 'onedrive' || $media->platform == 'dropbox' || $media->platform == 'box')
        @if ($media->type == \App\Media::TYPE_IMAGE)
    	    <div
                src="url({{$media->file_path}})"></div>
        @elseif ($media->type == \App\Media::TYPE_VIDEO)
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/video.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
        @elseif ($media->type == \App\Media::TYPE_AUDIO)
            <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width.'/text:Audio' ?>
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/audio.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
        @else
            <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/file.png') }})"
                {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
        @endif
    @else
        <div class="nf-thumbnail" style="background-image:url({{ \asset('assets/img/icons/') }}/{{ $media->platform }}.png"
            {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
    @endif

@else
    <?php $src = $defaultSrc ?: 'holder.js/'.$height.'x'.$width ?>
    <div class="nf-thumbnail" style="background-image:url({{ $src }})"
        {!! $style !!} {!! HTML::attributes($attributes) !!}></div>
@endif
