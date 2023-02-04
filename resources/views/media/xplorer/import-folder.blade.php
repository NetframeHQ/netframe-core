@extends('layouts.fullpage')

@section('content')
    <div class="card drives-import">
        <div class="card-header text-center">
            <a href="{{ session('landingDrivePage') }}" class="float-right">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('form.close') }}</span>
            </a>
            <h4>
                {{ trans('xplorer.folder.import.title') }}…
            </h4>
        </div>

        <div class="card-body drives-import-links">
            <a onclick="popup('{{$google_drive_url}}')">
                <img src="/assets/img/drives/drive.svg" alt="logo Drive">
                <p class="text">
                    <span>
                        {{ trans('xplorer.folder.import.title') }}
                    </span>
                    Google Drive
                </p>
            </a>
            <a onclick="popup('{{$dropbox_url}}')">
                <img src="/assets/img/drives/dropbox.svg" alt="logo Dropbox">
                <p class="text">
                    <span>
                        {{ trans('xplorer.folder.import.title') }}
                    </span>
                    Dropbox
                </p>
            </a>
            {{--
                <a onclick="popup('{{$onedrive_url}}')">
                    <img src="/assets/img/drives/onedrive.svg" alt="logo Onedrive">
                    <p class="text">
                        <span>
                            {{ trans('xplorer.folder.import.title') }}
                        </span>
                        OneDrive
                    </p>
                </a>
                <a onclick="popup('{{$box_url}}')">
                    <img src="/assets/img/drives/box.svg" alt="logo Box">
                    <p class="text">
                        <span>
                            {{ trans('xplorer.folder.import.title') }}
                        </span>
                        Box
                    </p>
                </a>
            --}}
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