<h2 class="Hn-title">Posts / médias {{ $periodType }}</h2>

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

    <h3 class="Hn-title">Médias</h3>
    <div id="mediaStatschart" style="height: 250px;"></div>

    <h3 class="Hn-title">Posts (news, events, offers)</h3>
    <div id="postStatschart" style="height: 250px;"></div>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="2" class="text-center">
                    Médias
                </th>
                <th colspan="2" class="text-center">
                    Posts (news, events, offers)
                </th>
            </tr>
            <tr>
                <th class="text-center">Période</th>
                <th class="text-center">Total</th>
                <th class="text-center">X<sup>ème</sup> conn.</th>
                <th class="text-center">Total</th>
                <th class="text-center">X<sup>ème</sup> conn.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($postMediaStatsGlobal as $period)
                <tr>
                    <td>{{ date('d / m / Y', strtotime($period['period'])) }}</td>
                    <td class="text-center">{{ $period['mediaTotal'] }}</td>
                    <td class="text-center">{{ $period['mediaXemeConn'] }}</td>
                    <td class="text-center">{{ $period['postTotal'] }}</td>
                    <td class="text-center">{{ $period['postXemeConn'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


@section('javascript')
@parent
    <script>
        var graph_data = {{json_encode(array_reverse($postMediaStatsGlobal))}};

        Morris.Bar({
            element: 'mediaStatschart',
            data: graph_data,
            xkey: 'period',
            xLabels: '{{ $periodType }}',
            ykeys: ['mediaTotal', 'mediaXemeConn'],
            labels: ['Total', 'Xème connexion']
          });

        Morris.Bar({
            element: 'postStatschart',
            data: graph_data,
            xkey: 'period',
            xLabels: '{{ $periodType }}',
            ykeys: ['postTotal', 'postXemeConn'],
            labels: ['Total', 'Xème connexion']
          });
    </script>
@stop