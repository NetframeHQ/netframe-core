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
                                {{ trans('email.cron.welcome', ['userName' => $user->firstname], $user->lang) }},
                                <br><br>
                                {{ trans('email.api.'.$result.'.content', [], $user->lang) }}
                                <br><br>
                                {{ trans('email.cron.signature', [], $user->lang) }}
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