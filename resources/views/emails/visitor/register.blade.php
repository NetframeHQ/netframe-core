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
                                        <strong>{{ $user->getNameDisplay() }}  {{ trans('email.visitor.invite.title') }}</strong>
                                        <br><br>
                                        {{ trans('email.visitor.invite.content1') }}<br><br>
                                        <center>
                                            <a href="{{ $url }}">
                                                <strong>{{ trans('email.visitor.invite.join') }}</strong>
                                            </a>
                                        </center>
                                    </p>
                                    <br>
                                    <p>
                                        {{ trans('email.visitor.invite.rememberKey') }} :
                                    </p>
                                    <p style="word-break: break-all;">
                                        {{ $instance->getUrl() }}
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