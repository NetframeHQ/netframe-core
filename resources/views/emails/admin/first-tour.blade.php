@extends('emails.model')

@section('content')
    <table class="row">
        <tbody>
            <tr>
                <th class="small-12 large-12 columns first last">
                    <table>
                        <tbody>
                            <tr>
                                <th>
                                    <p style="text-align:center">
                                        <strong>Demande de rappel</strong>
                                        <br><br>
                                        Instance : {{ $instance->name }}<br>
                                        Utilisateur : {{ $user->getNameDisplay() }}<br>
                                        Téléphone : {{ $user->phone }}
                                    </p>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </th>
            </tr>
        </tbody>
    </table>
@stop