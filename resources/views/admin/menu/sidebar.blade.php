<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
        <li>
            <a href="{{ url()->route('admin.home') }}"><i class="glyphicon glyphicon-dashboard"></i> {{ trans('admin.menus.dashboard') }}</a>
        </li>
        <li>
            <a href="{{ url()->route('admin.instances.home') }}"><i class="glyphicon glyphicon-th-large"></i> {{ trans('admin.menus.instances') }}</a>
        </li>
        <li>
            <a href="{{ url()->to('/translations') }}"><i class="glyphicon glyphicon-flag"></i> {{ trans('admin.menus.translations') }}</a>
        </li>
        <li>
            <a href=""><i class="glyphicon glyphicon-th"></i> {{ trans('admin.menus.apps') }}</a>
        </li>
        <li>
            <a href="/admin/statistics/main-stats"><i class="glyphicon glyphicon-stats"></i> {{ trans('admin.menus.stats') }}</a>
        </li>
        {{--
        <li>
            <a data-toggle="collapse" data-target="#demo">
                <i class="glyphicon glyphicon-list-alt"></i> Dropdown <i class="caret"></i>
            </a>
            <ul id="demo" class="collapse">
                <li>
                    <a href="#">Dropdown Item</a>
                </li>
                <li>
                    <a href="#">Dropdown Item</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="blank-page.html"><i class="glyphicon glyphicon-folder-close"></i> Blank Page</a>
        </li>
        <li>
            <a href="index-rtl.html"><i class="glyphicon glyphicon-cloud"></i> RTL Dashboard</a>
        </li>
        --}}
    </ul>
</div>
<!-- /.navbar-collapse -->