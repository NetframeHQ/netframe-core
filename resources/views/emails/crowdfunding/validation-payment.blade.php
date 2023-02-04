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
                               {{ trans('email.crowdfunding.payment.funder.'.$result, [], $funder->user->lang) }}
                                @if($funder->refuseReason != null)
                                    <br><br>
                                    {{ $funder->refuseReason }}
                                @endif
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