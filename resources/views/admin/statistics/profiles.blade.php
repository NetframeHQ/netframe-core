<h2 class="Hn-title">Profils</h2>

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

    <div id="profilesStatschart" style="height: 250px;"></div>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th class="text-center">Période</th>
                <th class="text-center">Users</th>
                <th class="text-center">Talents</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profilesStatsGlobal as $period)
                <tr>
                    <td class="text-center">{{ date('d / m / Y', strtotime($period['period'])) }}</td>
                    <td class="text-center">{{ $period['nbUsers'] }}</td>
                    <td class="text-center">{{ $period['nbTalents'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


@section('javascript')
@parent
    <script>
        var graph_data = {{json_encode(array_reverse($profilesStatsGlobal))}};

        Morris.Bar({
            element: 'profilesStatschart',
            data: graph_data,
            xkey: 'period',
            ykeys: ['nbUsers', 'nbTalents'],
            labels: ['Users', 'Talents']
          });
    </script>
@stop