@if ($profileType == 'subscriptions')
    <?php
    $profile = $profile->profile;
    ?>
@endif

@if(!empty($profile))
<a href="{{ $profile->getUrl() }}" class="media-profile">
    <div class="media-content">
    @if(isset($profile->profileImage))
        {{ HTML::thumbnail($profile->profileImage, $width, $height, $attributes, $defaultSrc) }}
    @elseif (isset($profile->profile_media_id))
        <img src="{{ url()->route('media_download', array('id' => $profile->profile_media_id, 'thumb' => 1)) }}" {{ HTML::attributes($attributes) }}/>
    @else
        <img src="{{ asset('assets/img/avatar/'.$profile->getType().'.jpg') }}" alt="no-image" class="img-fluid" />
    @endif
    </div>
    <div class="media-caption text-center text-capitalize">
        {{ $profile->getNameDisplay() }}
    </div>
</a>
@endif