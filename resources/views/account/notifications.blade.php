@extends('account.main')

@section('subcontent')
    @foreach($notifiableDevices as $device=>$type)
        <div class="nf-form">
            <div class="nf-settings-title">
                {{ trans('user.notifications.devices.'.$device) }}
            </div>
            <div class="nf-form-informations">
                {{ trans('user.notifications.devices.'.$device.'Intro') }}
            </div>
            {{ Form::open() }}
                {{ Form::hidden('device', $device) }}
                @if($type == 'days')
                    <div class="nf-table-custom">
                        <div class="table-col">
                            <div class="table-line table-head-x">
                                @for($j=0;$j<=6;$j++)
                                    <div class="table-cell">
                                        <div class="table-cell-content">
                                            <div class="nf-checkbox">
                                            {{ trans('date.days.'.$j) }}
                                            </div>
                                        </div>
                                    </div>     
                                @endfor
                            </div>
                            <div class="table-line">
                                @for($j=0;$j<=6;$j++)
                                    <div class="table-cell">
                                        <div class="table-cell-content">
                                            <div class="nf-checkbox">
                                                {{ Form::checkbox('day_'.$j, '1', $userNotifications[$device][$j]) }}
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endif
                <div class="nf-form-validation">
                    <button type="submit" class="nf-btn btn-primary btn-xxl">
                        <div class="btn-txt">
                            {{ trans('form.save') }}
                        </div>
                        <div class="svgicon btn-img">
                            @include('macros.svg-icons.arrow-right')
                        </div>
                    </button>
                </div>
            {{ Form::close() }}
        </div>
    @endforeach
@stop