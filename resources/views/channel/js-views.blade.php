<!-- Template to display a single media imported-->
<script id="template-channels-menu" type="text/x-handlebars-template">
    <ul class="list-unstyled">
        @{{#each channels}}
            <li id="channel-@{{id}}">
                <a data-action="load-channel" data-channel-id="@{{id}}">
                    @{{name}}
                </a>
            </li>
        @{{/each}}
    </ul>
</script>

<script id="template-channels-menu-item" type="text/x-handlebars-template">
    <li>
        <a data-action="load-channel" data-channel-id="@{{id}}">
            @{{name}}
        </a>
    </li>
</script>
<script id="template-channels-search-users" type="text/x-handlebars-template">
    <ul class="sidebar-users list-unstyled" id="search-list">
        @{{#each users}}
            <li>
                <a class="sidebar-users-line" href="/channels/messenger/@{{id}}">
                    <span class="avatar">
                        @{{#if_eq profileImage null }}
                            <img src="/assets/img/avatar/user.jpg" style="max-width:25px;max-height:25px;width:100%;height:100%;" class="img-fluid float-left">
                        @{{else}}
                            <img src="/media/download/@{{profileImage}}?thumb=1" class="img-fluid float-left" style="max-width:25px;max-height:25px;width:100%;height:100%" />
                        @{{/if_eq}}
                    </span>
                    @{{name}}
                    @{{#if_eq online 'status-online' }}
                        <span class="@{{online}}"><i class="icon ticon-dot"></i></span>
                    @{{/if_eq}}
                </a>
            </li>
        @{{/each}}
    </ul>
</script>
