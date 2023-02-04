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
                                <strong>{{ trans('email.crowdfunding.subject', [], $user->lang) }}</strong>
                                <br><br>
                                {{ trans('email.crowdfunding.content'.$valid, [], $user->lang) }}
                                <br><br>
                                {{ trans('email.signatureTeam') }}
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
