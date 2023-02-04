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
                                        {!! trans('email.cron.boardingUserCanceled.content', [
                                            'instanceName' => $instance->name,
                                            'instanceAdmin' => $instanceAdmin->getNameDisplay(),
                                            'boardingLink' => $boarding->boardingUrl
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