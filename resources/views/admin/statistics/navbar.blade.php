<div class="row">
    <div class="col-md-12">
        <div class="float-left">
            <ul class="nav nav-pills">
                <li role="presentation" class="{{{ (Request::segment(3) == 'main-stats') ? 'active' : null }}}">
                    <a href="/admin/statistics/main-stats"> Accueil</a>
                </li>
                <li role="presentation" class="{{{ (Request::segment(3) == 'profiles') ? 'active' : null }}}">
                    <a href="/admin/statistics/profiles"> Profils</a>
                </li>
                <li role="presentation" class="{{{ (Request::segment(3) == 'revisits') ? 'active' : null }}}">
                    <a href="/admin/statistics/revisits"> Revisits</a>
                </li>
                <li role="presentation" class="{{{ (Request::segment(3) == 'posts-medias') ? 'active' : null }}}">
                    <a href="/admin/statistics/posts-medias"> Posts / médias</a>
                </li>
                <li role="presentation" class="{{{ (Request::segment(3) == 'top-users') ? 'active' : null }}}">
                    <a href="/admin/statistics/top-users"> Top users</a>
                </li>
                <li role="presentation" class="{{{ (Request::segment(3) == 'actions') ? 'active' : null }}}">
                    <a href="/admin/statistics/actions"> Actions</a>
                </li>
                <li role="presentation" class="{{{ (Request::segment(3) == 'logs') ? 'active' : null }}}">
                    <a href="/admin/statistics/logs"> Fichiers bruts</a>
                </li>
            </ul>
        </div>
    </div>
</div>