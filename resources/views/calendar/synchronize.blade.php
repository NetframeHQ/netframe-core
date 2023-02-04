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
    <div class="col-sm-8 offset-sm-2 text-center ccc">
        <div class="synchronizing" style="display: none;">Synchronisation en cours...</div>
            @foreach($calendars as $calendar)
                <a class="calendar" data-email="{{$calendar->email}}" data-event="{{$event_id}}">
                    <div class="@if($calendar->type==0) google @else onedrive @endif">
                        <div class="drive">
                            <div class="text">{{$calendar->email}}</div>
                        </div>
                    </div>
                </a>
            @endforeach
    </div>
</div>

<script>
    function popup(url){
        window.open(url, 'popup', 'width=600,height=600');
    }
</script>