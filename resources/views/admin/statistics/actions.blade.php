<h2 class="Hn-title">Actions {{ $periodType }}</h2>

<h3 class="Hn-title text-center">
    <div class="float-right text-right">
            <a href="#search" data-toggle="collapse">
                <span class="caret"></span>
            </a>
            <div id="search" class="collapse form-inline">
                {{ Form::open(['id' => 'search', 'role' => 'form', 'method' => 'get']) }}

                    {{ Form::label('periodType', 'Période') }}
                    {{ Form::select('periodType', ['weekly' => 'Semaine', 'monthly' => 'Mois' ], $periodType, ['class' => 'form-control'])}}
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

    <div id="actionStatschart" style="height: 250px;"></div>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="2" class="text-center">
                    Total
                </th>
                <th colspan="2" class="text-center">
                    X<sup>ème</sup> conn.
                </th>
            </tr>
            <tr>
                <th class="text-center">Période</th>
                <th class="text-center">Total</th>
                <th class="text-center">Hors clip</th>
                <th class="text-center">Total</th>
                <th class="text-center">Hors clip</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actionsStatsGlobal as $period)
                <tr>
                    <td class="text-center">{{ date('d / m / Y', strtotime($period['period'])) }}</td>
                    <td class="text-center">{{ $period['actionTotal'] }}</td>
                    <td class="text-center">{{ $period['actionTotalWclip'] }}</td>
                    <td class="text-center">{{ $period['actionXemeConn'] }}</td>
                    <td class="text-center">{{ $period['actionXemeConnWclip'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


@section('javascript')
@parent
    <script>
        var graph_data = {{json_encode(array_reverse($actionsStatsGlobal))}};

        Morris.Bar({
            element: 'actionStatschart',
            data: graph_data,
            xkey: 'period',
            xLabels: '{{ $periodType }}',
            ykeys: ['actionTotal', 'actionTotalWclip', 'actionXemeConn', 'actionXemeConnWclip'],
            labels: ['Total', 'Total hors clip', 'Xème conn.', 'Xème conn. hors clip']
          });
    </script>
@stop