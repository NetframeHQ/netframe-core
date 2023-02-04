<h2 class="Hn-title">Top users {{ $periodType }} {{ date("d / m / Y", strtotime($refPeriod)) }}</h2>

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

<table class="table table-condensed">
    <thead>
        <tr>
            <th class="text-center" colspan="2">50 users les plus connectés</th>
        </tr>
        <tr>
            <th class="text-center">Nb conn.</th>
            <th class="text-center">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mostConnected as $user)
            <tr>
                <td class="text-center">{{ $user->value }}</td>
                <td class="text-center">{{ $user->libelle }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-condensed">
    <thead>
        <tr>
            <th class="text-center" colspan="2">20 users avec le plus de médias</th>
        </tr>
        <tr>
            <th class="text-center">Nb médias.</th>
            <th class="text-center">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mostMedias as $user)
            <tr>
                <td class="text-center">{{ $user->value }}</td>
                <td class="text-center">{{ $user->libelle }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-condensed">
    <thead>
        <tr>
            <th class="text-center" colspan="2">20 users avec le plus d'actions (like, clip, share, comment)</th>
        </tr>
        <tr>
            <th class="text-center">Nb actions</th>
            <th class="text-center">User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mostActions as $user)
            <tr>
                <td class="text-center">{{ $user->value }}</td>
                <td class="text-center">{{ $user->libelle }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
