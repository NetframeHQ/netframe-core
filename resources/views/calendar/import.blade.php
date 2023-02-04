@extends('layouts.fullpage')

@section('favicon')
{{url()->route('netframe.svgicon', ['name' => 'calendar'])}}
@endsection

@section('content')
    <div class="card drives-import">
        <div class="card-header text-center">
            <a href="{{ session('landingCalendarPage') }}" class="float-right">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('form.close') }}</span>
            </a>
            <h4>
                {{ trans('posting.TEvent.importFrom') }}…
            </h4>
        </div>

        <div class="card-body drives-import-links">
            <a onclick="popup('{{$google_calendar}}')">
                <img src="/assets/img/drives/gcalendar.svg" alt="logo Google Calendar">
                <p class="text">
                    <span>
                        {{ trans('posting.TEvent.importFrom') }}
                    </span>
                    Google Calendar
                </p>
            </a>
            <a onclick="popup('{{$outlook}}')">
                <img src="/assets/img/drives/outlook.svg" alt="logo Outlook">
                <p class="text">
                    <span>
                        {{ trans('posting.TEvent.importFrom') }}
                    </span>
                    Outlook
                </p>
            </a>
        </div>
    </div>
@stop

@section('javascripts')
@parent
<script>
    function popup(url){
        window.open(url, 'popup', 'width=600,height=600');
    }
</script>
@stop
{{--
<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('posting.TEvent.importFrom') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->
<style>
        .drives{display: inline-block; width: 100%; margin-bottom: 30px}
        .google{background: #00a19a;}
        .google>div{background: url("{{asset('assets/img/google.png')}}") 20px center no-repeat; background-size: 40px}
        .onedrive>div{background: url("{{asset('assets/img/onedrive.png')}}") 20px center no-repeat; background-size: 40px}
        .onedrive{background: #00a19a}
        .drive{width: 100%; text-align: center; padding: 20px 30px; color: #fff; margin-top: 20px; display: block;}
    </style>

<div class="modal-body drives">
	<div class="col-sm-8 offset-sm-2 text-center">
        <a onclick="popup('{{$google_calendar}}')">
            <div class="google">
                <div class="drive">
                    <div class="text">Google Calendar</div>
                </div>
            </div>
        </a>

        <a onclick="popup('{{$outlook}}')">
            <div class="onedrive">
                <div class="drive">
                    <div class="text">Importer depuis Outlook</div>
                </div>
            </div>
        </a>

	</div>
</div>
--}}