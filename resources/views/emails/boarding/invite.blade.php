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
                                        <strong>{{ $boarding->userFrom->getNameDisplay() }}  {{ trans('boarding.emailContent.invite.title') }}</strong>
                                        <br><br>
                                        {{ trans('boarding.emailContent.invite.content1') }}<br><br>
                                        <center>
                                            <a href="{{ $boarding->boardingUrl }}">
                                                <strong>{{ trans('boarding.emailContent.invite.join') }}</strong>
                                            </a>
                                        </center>
                                    </p>
                                    <br>
                                    <p>
                                        {{ trans('boarding.emailContent.invite.rememberKey') }} :
                                    </p>
                                    <p style="word-break: break-all;">
                                        {{ $boarding->boardingUrl }}
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