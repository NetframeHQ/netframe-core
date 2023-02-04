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
                                        <strong>Modification de vote compte</strong>
                                        <br><br>
                                        Bonjour,<br><br>
                                        Suite à l'ouverture de vos accès hier et à un problème de doublon,
                                        votre espace utilisateur a été modifié sur "{{ $instance->name }}"<br><br>

                                        Voici vos informations de connexion mises à jour : <br>
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