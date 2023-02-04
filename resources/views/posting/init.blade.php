<div id="posting"></div>

@section('javascripts')
@parent
<script>

(function () {
    var postingSystem = $('#posting');
    new PostingSystem({
        $wrapper: postingSystem,
        $defaultTemplate: $('#template-post'),
        $defaultRoute: '{{ url()->route('posting.default') }}',
        $initFirstLoad: true,
        $profileId: {{ auth()->guard('web')->user()->id }},
        $profileType: 'user'
    });
})();

</script>
@stop
