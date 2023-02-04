<ul class="block-mosaic row">
@foreach($listProfile as $profile)
    <li class="mosaic-item col-md-{{ $colSize }} col-xs-{{ $colSize }}">
        <a href="{{ $profile->getUrl() }}">
            <div class="mosaic-content">
            @if(isset($profile->profile_media_id))
                <img src="{{ url()->route('media_download', ['id' => $profile->profile_media_id, 'thumb' => 1]) }}"
                    class="img-fluid"
                />
            @else
                <img src="{{ asset('assets/img/avatar/'.$profile->getType().'.jpg') }}" alt="no-image" class="img-fluid" />
            @endif
            </div>
            <div class="mosaic-footer">
                <p class="thumb-mosaic-category">
                    {{ $profile->getNameDisplay() }}
                </p>
            </div>
        </a>
    </li>
@endforeach
</ul>
