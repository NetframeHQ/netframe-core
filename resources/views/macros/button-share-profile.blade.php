@if(!$inPost)
    <a 
        href="{{ url()->to('netframe/form-share-profile', ['profileType' => class_basename($profile), 'profileId' => $profile->id]) }}"
        class="nf-btn btn-xl fn-netframe-share"
        data-toggle="modal"
        data-target="#modal-ajax"
    >
        <span class="svgicon btn-img">
            @include('macros.svg-icons.share')
        </span>
        <span class="btn-txt">
            {{ trans("netframe.share") }}
        </span>
        <span class="btn-label">
            {{ $profile->share }}
        </span>
    </a>
@else
    <a 
        href="{{ url()->to('netframe/form-share-profile', ['profileType' => class_basename($profile), 'profileId' => $profile->id]) }}"
        class="nf-btn fn-netframe-share"
        data-toggle="modal"
        data-target="#modal-ajax"
    >
        <span class="svgicon icon-share">
            @include('macros.svg-icons.share')
        </span>
        <span class="btn-txt btn-digit">
            {{ $profile->share }}
        </span>
        <span class="btn-txt">
            {{ trans("netframe.share") }}
        </span>
    </a>
@endif