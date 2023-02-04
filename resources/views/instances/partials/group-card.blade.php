<li class="bd-bottom padding-5 clearfix member-card container-element group-{{ $group->id }}">
    <div class="float-left mg-left-10">
        <strong>{{ $group->name }}</strong>
    </div>
    <div class="float-right">
        <a href="{{ url()->route('instance.groups', ['id' => $group->id]) }}">
            <span class="svgicon icon-menu">
                @include('macros.svg-icons.menu')
            </span>
        </a>
    </div>
</li>