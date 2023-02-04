<div class="d-flex flex-wrap">
    <!-- AVATAR -->
    <div class="nf-form-cell nf-cell-avatar nf-cell-logo-square @if(isset($typeTheme)) theme-{{ $typeTheme }} @endif">
        <div class="nf-form-select" id="instance-menu-logo{{ ((isset($typeTheme) && $typeTheme == 'dark') ? '-dark': '') }}">
            <a href="#" class="nf-btn btn-submenu">
                <img src="{{ $menuLogo }}" class="btn-img {{ (isset($typeTheme) && $typeTheme == 'dark') ? 'instance-menu-logo-dark' : 'instance-menu-logo' }} @if($menuLogo == null) d-none @endif" data-default-img="{{ $defaultMenuLogo }}">
                <span class="btn-img svgicon">
                    @include('macros.svg-icons.arrow-down')
                </span>
            </a>
            <div class="submenu-container submenu-left">
                <ul class="submenu">
                    <li>
                        <label class="nf-btn" id="profile-picture">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.plus')
                            </span>
                            <span class="btn-txt">
                                {{ trans('profiles.manage.uploadLogo') }}
                            </span>
                            @include('instances.partials.form-upload', [
                                'image' => (isset($typeTheme) && $typeTheme == 'dark') ? 'instance-menu-logo-dark' : 'instance-menu-logo',
                                'finalField' => (isset($typeTheme) && $typeTheme == 'dark') ? 'menuLogoFileDark' : 'menuLogoFile',
                                'customButton' => '',
                            ])
                        </label>
                    </li>
                    <li class="fn-remove-avatar @if($menuLogo == $defaultMenuLogo) d-none @endif ">
                        <a class="nf-btn" id="fn-delete-instance-img" data-image-type="{{ (isset($typeTheme) && $typeTheme == 'dark') ? 'instance-menu-logo-dark' : 'instance-menu-logo' }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.trash')
                            </span>
                            <span class="btn-txt">
                                {{ trans('profiles.manage.resetLogo') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <span class="nf-form-label">
            {{ trans('instances.graphical.menuLogo') }}
        </span>
    </div>

    <!-- LOGO RECTANGLE -->
    <div class="nf-form-cell nf-cell-avatar nf-cell-logo-rectangle @if(isset($typeTheme) )theme-{{ $typeTheme }} @endif">
        <div class="nf-form-select" id="instance-main-logo{{ ((isset($typeTheme) && $typeTheme == 'dark') ? '-dark': '') }}">
            <a href="#" class="nf-btn btn-submenu" id="profile-image-container">
                <img src="{{ $mainLogo }}" class="btn-img {{ ($typeTheme == 'dark') ? 'instance-main-logo-dark' : 'instance-main-logo' }} @if($mainLogo == null) d-none @endif"  data-default-img="{{ $defaultMainLogo }}">
                <span class="btn-img svgicon">
                    @include('macros.svg-icons.arrow-down')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    <li>
                        <label class="nf-btn" id="profile-picture">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.plus')
                            </span>
                            <span class="btn-txt">
                                {{ trans('profiles.manage.uploadLogo') }}
                            </span>
                            @include('instances.partials.form-upload', [
                                'image' => (isset($typeTheme) && $typeTheme == 'dark') ? 'instance-main-logo-dark' : 'instance-main-logo',
                                'finalField' => (isset($typeTheme) && $typeTheme == 'dark') ? 'mainLogoFileDark' : 'mainLogoFile',
                            ])
                        </label>
                    </li>
                    <li class="fn-remove-avatar @if($mainLogo == $defaultMainLogo) d-none @endif">
                        <a class="nf-btn" id="fn-delete-instance-img" data-image-type="{{ ($typeTheme == 'dark') ? 'instance-main-logo-dark' : 'instance-main-logo' }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.trash')
                            </span>
                            <span class="btn-txt">
                                {{ trans('profiles.manage.resetLogo') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <span class="nf-form-label">
            {{ trans('instances.graphical.mainLogo') }}
        </span>
    </div>
</div>