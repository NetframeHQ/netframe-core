<ul class="list-inline d-md-flex justify-content-center login-links align-items-end">
    <li class="list-inline-item">
        <a href="{{ url()->route('static_cgv') }}">{{ trans('links.cgv') }}</a>
    </li>
    <li class="list-inline-item">
        <a href="{{ url()->route('static_cgu') }}">{{ trans('links.cgu') }}</a>
    </li>
    <li class="list-inline-item">
        <a href="{{ url()->route('static_contacts') }}">{{ trans('links.contacts') }}</a>
    </li>
</ul>