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
                                {{ trans('email.newAccountent.welcome') }} {{ $email }},
                                <br><br>
                                {{ trans('email.newAccountent.access') }} 
                                <br><br>
                                {{ trans('email.newAccountent.toLink') }} <a style="text-decoration:none; color:#2199e8" href="{{ $url }}">{{ $url }}</a>.
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
