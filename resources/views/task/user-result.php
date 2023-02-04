<script id="template-tasks-search-users" type="text/x-handlebars-template">
    <ul class="sidebar-users list-unstyled" id="search-list">
        {{#each users}}
            <li>
                <div
                    class="nf-btn btn-nobg user select-user"
                    title="{{ name }}"
                    data-user-id="{{id}}"
                >
                    {{#if_eq profileImage null }}
                        <span class="user-avatar-initials size-20 avatar float-left select-task-user" style="background-color:{{initialsToColor}}">
                            <span class="initials-letters">
                                {{initials}}
                            </span>
                        </span>
                    {{else}}
                        <span class="avatar">
                            <div class="nf-thumbnail user" style="background-image:url(/media/download/{{profileImage}}?thumb=1)"></div>
                        </span>
                    {{/if_eq}}
                    <span class="btn-txt">
                        {{name}}
                    </span>
                </div>
            </li>
        {{/each}}
    </ul>
</script>