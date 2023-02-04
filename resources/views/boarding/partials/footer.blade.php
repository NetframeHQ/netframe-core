<footer>
    <ul class="steps">
        <li class="{{ ($stepBoarding == 1) ? 'is-active' : ''}}">1</li>
        <li class="{{ ($stepBoarding == 2) ? 'is-active' : ''}}">2</li>
        @if(!isset($nbSteps) || $nbSteps > 2)
            <li class="{{ ($stepBoarding == 3) ? 'is-active' : ''}}">3</li>
            <li class="{{ ($stepBoarding == 4) ? 'is-active' : ''}}">4</li>
        @endif
    </ul>

    <div class="text-center">
        @include('static.links')
    </div>
</footer>