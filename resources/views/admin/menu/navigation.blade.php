<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{{ url()->route('admin.home') }}" data-toggle="tooltip" data-placement="bottom" title="Netframe Admin">
        <img class="float-left" src="{{ asset('assets/img/logo.png') }}" alt="logo netframe" width="30" height="30" />
    </a>

</div>

<!-- Top Menu Items -->
<ul class="nav navbar-right top-nav">
    <!-- Loader -->
    <li class="navbar-loader">
        <figure class="spinner-svg hide">
            <img src="{{ asset('assets/img/netframe-loader.svg') }}" alt="" class="img-fluid" />
        </figure>
    </li>
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown">
            <i class="glyphicon glyphicon-user"></i>
            {{ auth()->guard('admin')->user()->username }} - {{ auth()->guard('admin')->user()->email }} <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ url()->route('admin.admins') }}">
                    <i class="glyphicon glyphicon-cog"></i> {{ trans('admin.menus.adminList') }}
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{{ url()->route('admin.logout') }}">
                    <i class="glyphicon glyphicon-off"></i> {{ trans('admin.menus.logout') }}
                </a>
            </li>
        </ul>
    </li>
</ul>
