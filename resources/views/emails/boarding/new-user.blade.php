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
                                        <strong>{{ trans('boarding.emailContent.newUser.title') }}</strong>
                                    </p>
                                    <p style="text-align:left">
                                        <strong>
                                            {{ trans('boarding.emailContent.newUser.content1') }}
                                        </strong>
                                    </p>
                                    <p style="text-align:left">
                                        {{ trans('boarding.emailContent.newUser.content2') }}<br>
                                        <center>
                                            <a href="{{ $instance->getUrl() }}">
                                                <strong>{{ trans('boarding.emailContent.newUser.txtButton') }}</strong>
                                            </a>
                                        </center>

                                        {{ trans('boarding.emailContent.newUser.content3') }}<br>
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