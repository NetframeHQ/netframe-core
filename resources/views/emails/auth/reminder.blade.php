@extends('emails.model')

@section('content')
    <table class="row">
        <tbody>
            <tr>
                <th class="small-12 large-12 columns first last">
                    <table>
                    <tbody><tr>
                        <th>
                            <p style="text-align:left">
                                {{ trans('email.resetPassword.welcome', ['userName' => $user->firstname]) }},
                                <br><br>
                                {{ trans('email.resetPassword.toLink') }} <a style="text-decoration:none; color:#2199e8" href="{{ $uriToken }}">{{ $uriToken }}</a>.
                                <br><br>
                                {{ trans('email.resetPassword.linkExpire', ['expireTime' => config('auth.timeout_password')]) }}
                                <br><br>
                                {{ trans('email.signatureTeam') }}
                            </p>
                        </th>
                        <th class="expander"></th>
                    </tr>
                    </tbody></table>
                </th>
            </tr>
        </tbody>
    </table>
@stop
