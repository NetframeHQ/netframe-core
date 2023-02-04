
{{-- HEADER OF LAST24 ACTIVITY --}}

<div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.last24_big')
        </span>
        <h2 class="main-header-title">{{ trans('netframe.leftMenu.24h') }}</h2>
    </div>

    {{-- CUSTOM LINKS PAGE LAST24 ACTIVITY --}}
    <ul class="nf-actions">
        {{-- SIDEBAR TOGGLE --}}
        <li class="nf-action">
            <a href="#" class="content-sidebar-toggle nf-btn btn-ico">
                <span class="btn-img svgicon fn-close">
                    @include('macros.svg-icons.sidebar-close2')
                </span>
                <span class="btn-txt fn-close">
                    {{ trans('netframe.close_sidebar') }}
                </span>
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.sidebar-open')
                </span>
                <span class="btn-txt">
                    {{ trans('netframe.open_sidebar') }}
                </span>
            </a>
        </li>
    </ul>
</div>