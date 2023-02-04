@if(count($profileCommunity) == 0)
<ul class="nf-list-settings">
    <li class="nf-list-placeholder">
        {{ trans('members.noUsers.search') }}
    </li>
</ul>
@else
    <ul class="nf-list-settings">
        @include('join.users-list')
    </ul>
@endif