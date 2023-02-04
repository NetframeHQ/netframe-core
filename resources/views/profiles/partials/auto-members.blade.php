<label class="nf-form-cell nf-form-checkbox">
    {{ Form::checkbox('auto_member', 1, ($profile->auto_member != 0), ['id' => 'auto_member', 'class' => 'nf-form-input']) }}
    <span class="nf-form-label">
        {{ trans('profiles.manage.autoMember') }}
    </span>
    <div class="nf-form-cell-fx"></div>
</label>

<label class="nf-form-cell">
    {{ Form::select('auto_member_role', $roles, $profile->auto_member, ['class' => 'nf-form-input']) }}
    <span class="nf-form-label">
        {{ trans('profiles.manage.autoMemberStatus') }}
    </span>
    <div class="nf-form-cell-fx"></div>
</label>