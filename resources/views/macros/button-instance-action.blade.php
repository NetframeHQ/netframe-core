<li>
    <a class="nf-btn" href="{{route('instance.edit', ['id' => $user_id])}}" data-toggle="modal" data-target="#modal-ajax">
        <span class="btn-txt">
            {{ trans('instances.profiles.change.edit') }}
        </span>
    </a>
</li>
<li>
    <a class="nf-btn" href="{{url()->route('instance.manageRights', ['id' => $user_id])}}">
        <span class="btn-txt">
            {{trans('instances.profiles.manage-user')}}
        </span>
    </a>
</li>
@if(isset($activeVirtualUsers) && $activeVirtualUsers)
    <li>
        <a class="nf-btn" href="{{url()->route('instance.virtualuser.list', ['userId' => $user_id])}}">
            <span class="btn-txt">
                {{trans('instances.profiles.manageVirtualUser')}}
            </span>
        </a>
    </li>
@endif
