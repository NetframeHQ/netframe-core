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
                                    <p style="text-align:left">
                                        <strong>Ouverture de vote compte</strong>
                                        <br><br>
                                        Bonjour,<br><br>
                                        Votre espace utilisateur a été ouvert sur "{{ $instance->name }}"<br><br>

                                        Voici vos informations de connexion : <br>
                                        Nom d'utilisateur : {{ $user->email }}<br>
                                        Mot de passe : {{ $password }}
                                        <br><br>

                                        <center>
                                            <strong>
                                                <a href="{{ $instance->getUrl() }}">Pour vous connecter cliquez ici</a>
                                            </strong>
                                        </center>
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