<h2 class="Hn-title">Reporting</h2>
@if(count($reportsPdf) > 0)
    <ul>
        @foreach($reportsPdf as $key=>$report)
            <li><a href="{{ url()->route('admin.report', [$report]) }}" target="_blank">{{ $report }}</a>
        @endforeach
    </ul>
@endif