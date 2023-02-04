@extends('emails.model')

@section('content')
    <table class="row">
        <tbody>
            <tr>
                <th class="small-12 large-12 columns first last">
                    <table>
                        <tbody>
                            <tr>
                                <tr>
                                    <p style="text-align:left">
                                        {!! trans('email.cron.paymenterror.content', ['instance_subscription' => $instance_subscription]) !!} 
                                    </p>
                                    <p>
                                        {!! trans('email.cron.signature') !!}
                                    </p>
                                </tr>
                            </tr>
                        </tbody>
                    </table>
                </th>
            </tr>
        </tbody>
    </table>
@stop