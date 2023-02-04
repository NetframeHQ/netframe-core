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
                                        {{ trans('email.cron.welcome', ['userName' => $user->firstname], $user->lang) }},
                                    </p>

                                    {{ trans('email.cron.workflowAction.content') }}
                                    <ul>
                                        <li>
                                            {{ $workflow->media()->name }} {{ trans('email.cron.workflowAction.before') }} {{ date('d / m / Y', strtotime($wfAction->action_date)) }}
                                        </li>
                                    </ul>

                                    <p style="text-align:left">
                                        {{ trans('email.cron.workflowAction.validInfos') }}
                                    </p>

                                    <p style="text-align:left">
                                        <a style="text-decoration:none; color:#2199e8" href="{{ $instance->getUrl() }}">{{ trans('email.cron.notifMessages.goOnNetframe', [], $user->lang) }}</a>
                                    </p>
                                </th>
                            </tr>
                            <tr>
                                <th class="expander"></th>
                            </tr>
                        </tbody>
                    </table>
                </th>
            </tr>
        </tbody>
    </table>
@stop