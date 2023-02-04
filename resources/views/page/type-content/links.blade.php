@foreach($post->links as $link)
    <a href="{{ $link->final_url }}" target="_blank" class="link-preview">
        <div class="link-visual" style="background-image:url('{{ url()->route('link.download', ['id' => $link->id]) }}');">
            <!-- <img src="{{ url()->route('link.download', ['id' => $link->id]) }}" class="img-fluid"> -->
        </div>
        <div class="link-infos">
            <div class="link-info">
                <h4 class="link-info-title">{{ $link->title }}</h4>
                <p class="link-info-desc">{{ $link->description }}</p>
            </div>
            <div class="link-links">
                <p class="link-info-url">
                    {{ $link->url }}
                </p>
                <!-- <p class="nf-btn">
                    <span class="btn-txt">
                        {{ trans('page.openLink') }}
                    </span>
                </p> -->
            </div>
        </div>
    </a>
@endforeach