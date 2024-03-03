<ul class="list-inline d-lg-flex justify-content-center login-links align-items-start">
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