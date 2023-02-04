<label class="nav-search">
    {{ Form::open(array('route' => 'search', 'class' => '', 'method' => 'GET')) }}
    {{ Form::hidden('loadFilters', '0') }}
    {{ Form::input
        (
        'text',
        'query',
        null,
        [
            'id' => 'search-input',
            'class' => 'search-input',
            'placeholder' => trans('netframe.searchPlaceholder'),
            'autocomplete' => 'off'
        ]
        )
    }}
    <button type="submit" value="Submit" title="{{ trans('netframe.search') }}" class="nf-btn btn-ico btn-nobg">
        <span class="btn-img svgicon">
            @include('macros.svg-icons.search3')
        </span>
    </button>
    @if($userOs!= '')
        <span class="search-key">
            @if($userOs === 'mac')
                {{ trans('netframe.oshelper.search.mac') }}
            @else
                {{ trans('netframe.oshelper.search.linux') }}
            @endif
        </span>
    @else
    @endif
    {{ Form::close() }}
</label>

@section('javascripts')
    @parent
    <script src="/js/autocomplete.js" type="text/javascript"></script>
@stop
