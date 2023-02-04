@if(\Lang::getLocale() != 'fr')
    <link rel="alternate" href="{{ Request::url() }}?refLang=fr" hreflang="fr" />
@endif
@if(\Lang::getLocale() != 'en')
    <link rel="alternate" href="{{ Request::url() }}?refLang=en" hreflang="en" />
@endif
@if(\Lang::getLocale() != 'es')
    <link rel="alternate" href="{{ Request::url() }}?refLang=es" hreflang="es" />
@endif