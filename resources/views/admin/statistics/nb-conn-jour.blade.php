<h2 class="Hn-title">Nb conn. / jour apres 1<sup>ère</sup> connexion</h2>

<h3 class="Hn-title text-center">
    <div class="float-right text-right">
            <a href="#search" data-toggle="collapse">
                <span class="caret"></span>
            </a>
            <div id="search" class="collapse form-inline">
                {{ Form::open(['id' => 'search', 'role' => 'form', 'method' => 'get']) }}
                    {{ Form::label('start_date', 'Du') }}
                    {{ Form::date('start_date', $startDate, 'form-control') }}
                    {{ Form::label('end_date', 'Au') }}
                    {{ Form::date('end_date', $endDate, 'form-control') }}
                    {{ Form::submit('Rechercher') }}
                {{ Form::close() }}
        </div>
    </div>

    Du {{ date('d / m / Y', strtotime($startDate)) }} au {{ date('d / m / Y', strtotime($endDate)) }}

</h3>

@if($dailyStats->count() > 0)
    <div id="dailyStatschart" style="height: 250px;"></div>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th class="text-center">Jour</th>
                <th class="text-center">Nb. conn.</th>
            </tr>
        </thead>
        <tbody>
        @foreach($dailyStats as $dailyStat)
            <tr>
                <td class="text-center">{{ date('d / m / Y', strtotime($dailyStat->jour)) }}</td>
                <td class="text-center">{{ round($dailyStat->value) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

@section('javascript')
@parent
    <script>
    new Morris.Line({
        // ID of the element in which to draw the chart.
        element: 'dailyStatschart',
        // Chart data records -- each entry in this array corresponds to a point on
        // the chart.
        data: {{json_encode($dailyStats)}},
        // The name of the data record attribute that contains x-values.
        xkey: 'jour',
        // A list of names of data record attributes that contain y-values.
        ykeys: ['value'],
        // Labels for the ykeys -- will be displayed when you hover over the
        // chart.
        labels: ['Value']
      });
    </script>
@stop