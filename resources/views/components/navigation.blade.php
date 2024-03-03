<div id="navigation" class="navigation {{ $navThemeInstance }}">
    <button class="sidebar-toggle d-md-none">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <a href="{{ url()->route('portal') }}" title="{{ trans('netframe.leftMenu.home') }}" class="navigation-logo">
        @if(isset($menuLogo))
            <div style="background-image: url('{{ $menuLogo }}')" class="logo-img nf-logosquare menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @else
            <div style="background-image: url('{{ asset('assets/img/logo.png') }}')" class="logo-img nf-logosquare menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @endif

        @if(isset($menuLogoDark))
            <div style="background-image: url('{{ $menuLogoDark }}')" class="logo-img nf-logosquare menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @else
            <div style="background-image: url('{{ asset('assets/img/logo.png') }}')" class="logo-img nf-logosquare menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}" alt="Logo"></div>
        @endif
    </a>

    @include('components.menu-start')

    @include('components.menu-end')

</div>
