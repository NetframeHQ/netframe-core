<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('posting.TEvent.exportTo') }}
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
        @foreach($calendars as $calendar)
            @if($calendar->type == 0)
                <a href="{{route('calendar.launchExport', ['id' => $calendar->type, 'email' => $calendar->email])}}">
                    <div class="@if($calendar->type==0) google @else onedrive @endif">
                        <div class="drive">
                            <div class="text">{{$calendar->email}}</div>
                        </div>
                    </div>
                </a>
            @endif
        @endforeach

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

<script>
    function popup(url){
        window.open(url, 'popup', 'width=600,height=600');
    }
</script>