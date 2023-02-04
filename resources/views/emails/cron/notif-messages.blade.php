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
                                    <br><br>
                                    {{ trans('email.cron.notifMessages.content', [], $user->lang) }}
                                </p>

                                <ul>
                                    @if($user->nbNotifs > 0)
                                        <li style="text-align:left">{{ trans_choice('email.cron.notifMessages.contentNotif', $user->nbNotifs, ['nbNotif' => $user->nbNotifs], $user->lang) }}</li>
                                    @endif
                                    @if($user->nbMessages > 0)
                                        <li style="text-align:left">{{ trans_choice('email.cron.notifMessages.contentMessages', $user->nbMessages, ['nbMsg' => $user->nbMessages], $user->lang) }}</li>
                                    @endif
                                    @if($user->nbChanMessages > 0)
                                        <li style="text-align:left">{{ trans_choice('email.cron.notifMessages.contentChanMessages', $user->nbChanMessages, ['nbChanMessages' => $user->nbChanMessages], $user->lang) }}</li>
                                    @endif
                                </ul>

                                <p style="text-align:left">
                                    <a style="text-decoration:none; color:#2199e8" href="{{ $instance->getUrl() }}">{{ trans('email.cron.notifMessages.goOnNetframe', [], $user->lang) }}</a>
                                </p>
                            </th>
                        </tr>
                        <tr>
                            <th class="expander"></th>
                        </tr>
                        <tr>
                            <th>
                                <p style="text-align:left; font-size:10px;">
                                    {!! trans('email.cron.notifMessages.manageNotifs') !!}
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