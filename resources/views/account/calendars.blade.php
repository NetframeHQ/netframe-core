@extends('account.main')

@section('subcontent')
    <div class="nf-form">
        <div class="nf-settings-title">
            {{ trans('user.menu.calendars') }}

            <ul class="nf-actions">
                <li class="nf-action">
                    <a class="nf-btn" target="_blank" href="{{ url()->route('calendar.import') }}">
                        <span class="btn-img svgicon">
                            @include('macros.svg-icons.import')
                        </span>
                        <span class="btn-txt">
                            {{ trans('posting.TEvent.import') }}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        @if (count($calendars) >= 1)
            <ul class="nf-list-settings">
                @foreach($calendars as $calendar)
                    <li class="nf-list-setting">
                        <span class="svgicon">
                            @include('macros.svg-icons.calendar')
                        </span>
                        <div class="nf-list-infos">
                            <div class="nf-list-title">
                                {{$calendar->email}}
                            </div>
                        </div>
                        <ul class="nf-actions">
                            <li class="nf-action">
                                <button class="nf-btn btn-ico fn-delete-calendar" type="button" aria-label="Close" data-tl-delete="{{$calendar->email}}" title="Effacer">
                                    <span class="nf-img svgicon">
                                        @include('macros.svg-icons.trash')
                                    </span>
                                </button>
                            </li>
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@stop

@section('javascripts')
@parent
<script>
	$(document).on('click', '.fn-delete-calendar', function(e) {
        var _confirm = confirm('{{trans("user.calendar.confirmDelete")}}');

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
        else{
            e.preventDefault();
            var el = $(this);
            var panel = el.closest(".calendar");

            var calendar = el.data('tl-delete');

            var jqXhr = $.post("{{route('account.calendars')}}" , {
                postData : calendar
            });

            jqXhr.success(function(data) {
                if(calendar){
                    panel.fadeOut();
                }
            });
        }
    });
</script>
@endsection