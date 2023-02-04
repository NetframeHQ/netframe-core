@if(count($medias) == 1)
    @include('page.type-content.medias.medias')
@else
    @include('page.type-content.medias.multi-medias')
@endif
