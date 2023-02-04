<li>
    <a href="{{ $profile->getUrl() }}">
        {!! HTML::profileIcon($profile->getType()) !!}
        <span>{{ $profile->getNameDisplay() }}</span>
    </a>
</li>