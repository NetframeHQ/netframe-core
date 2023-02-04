<div class="card">
    <div class="card-body">
        @if(session()->has('instanceRoleId') && session('instanceRoleId') == 1)
            <p>
                Your workspace is ready, you can start publishing content, create groups, share information.
            </p>
            <p>
                To invite users, customize your workspace, go to the menu at the top right and click on "<a href="{{ url()->route('instance.parameters') }}">workspace settings</a>"
            </p>
        @else
            <p>
                Your account is created, you can start publishing content, create groups, share information.
            </p>
        @endif
    </div>
</div>