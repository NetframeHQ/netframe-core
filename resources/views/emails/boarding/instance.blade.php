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
                                        <strong>{{ trans('boarding.emailContent.instance.title') }}</strong>
                                    </p>
                                    <p style="text-align:left">
                                        <strong>
                                            {{ trans('boarding.emailContent.instance.content1') }}
                                        </strong>
                                    </p>
                                    <p style="text-align:left">
                                        {{ trans('boarding.emailContent.instance.content2') }}<br>
                                        {{ $instance->getUrl() }}
                                    </p>
                                    <p style="text-align:left">
                                        {{ trans('boarding.emailContent.instance.content3') }}<br>
                                        {{ $instance->getBoardingUrl() }}
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