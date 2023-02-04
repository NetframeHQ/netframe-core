<h2 class="Hn-title">Revisits {{ $periodType }}</h2>

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

    <div id="revisitStatschart" style="height: 250px;"></div>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th>Période</th>
                <th>1 conn. / période</th>
                <th>2 conn. / période</th>
                <th>2+ conn. / période</th>
                <th>5+ conn. / période</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revisitStatsGlobal as $period)
                <tr>
                    <td>{{ date('d / m / Y', strtotime($period['period'])) }}</td>
                    <td class="text-center">{{ $period['1connexion'] }}</td>
                    <td class="text-center">{{ $period['2connexion'] }}</td>
                    <td class="text-center">{{ $period['2Mconnexion'] }}</td>
                    <td class="text-center">{{ $period['5Mconnexion'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


@section('javascript')
@parent
    <script>
        var graph_data = {{json_encode($revisitStatsGlobal)}};
        Morris.Line({
            element: 'revisitStatschart',
            data: graph_data,
            xkey: 'period',
            xLabels: '{{ $periodType }}',
            ykeys: ['1connexion', '2connexion', '2Mconnexion', '5Mconnexion'],
            labels: ['1 connexion', '2 connexion', '2+ connexion', '5+ connexion']
        });
    </script>
@stop