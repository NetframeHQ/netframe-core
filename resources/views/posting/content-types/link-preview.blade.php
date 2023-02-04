<div class="panel-body link-preview" data-id="{{ $link->id }}" id="import-link-{{ $link->id }}">
    <div class="link-visual" style="background-image:url('{{ url()->route('link.download', ['id' => $link->id]) }}');">
    </div>
    <div class="link-infos">
        <div class="nf-close fn-remove-link">
        </div>
        <div class="link-info">
            <h4 class="link-info-title">{{ $link->title }}</h4>
            <p class="link-info-desc">{{ $link->description }}</p>
        </div>
        <div class="link-links">
            <p class="link-info-url">{{ $link->url }}</p>
        </div>
    </div>
</div>