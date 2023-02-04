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
                                    <p>
                                        {!! trans('email.cron.notConnectedAdmin.content', [
                                            'instanceName' => $instance->name,
                                            'nbWeek' => trans_choice('email.cron.notConnectedAdmin.nbWeeks', $nbWeek),
                                            'instanceUrl' => $instance->getUrl()
                                        ]) !!}
                                    </p>
                                    <p>
                                        {!! trans('email.cron.signature2') !!}
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