@extends('office.layout')

@section('subcontent')
    <div class="main-container no-side">
        <div id="nav_skipped" class="main-scroller">
            <div class="search">
                <div class="text-center">
                    <ul class="clearFix list-unstyled">
                        <li>
                            <a class="try-editor document" data-toggle="modal" data-target="#modal-ajax" href="{{route('office.create',['documentType'=>'document'])}}">{{trans('office.create')}}
                                <br />
                                {{trans('office.document')}}</a>
                        </li>
                        <li>
                            <a class="try-editor spreadsheet" data-toggle="modal" data-target="#modal-ajax" href="{{route('office.create',['documentType'=>'spreadsheet'])}}">{{trans('office.create')}}
                                <br />
                                {{trans('office.spreadsheet')}}</a>
                        </li>
                        <li>
                            <a class="try-editor presentation" data-toggle="modal" data-target="#modal-ajax" href="{{route('office.create',['documentType'=>'presentation'])}}">{{trans('office.create')}}
                                <br />
                                {{trans('office.presentation')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="netframe-list-wrapper">
                    <ul class="netframe-list file-display">
                        @foreach ($medias as $media)
                            @include('office.file')
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
@section('subscripts')
<script>
$( document ).ajaxSuccess(function( event, xhr, settings ) {
  if(xhr.responseJSON.route)
    window.open(xhr.responseJSON.route, '_blank')
});
</script>
@stop