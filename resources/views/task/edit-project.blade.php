@extends('layouts.page')
@section('title')
    {{ trans('task.title') }} • {{ $globalInstanceName }}
@stop
@section('content')
<div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.user')
        </span>
        <h2 class="main-header-title">
            {{ trans('task.title') }}
        </h2>
    </div>
    <ul class="nf-actions">
        <li class="nf-action">
            <a class="nf-btn btn-ico" href="{{route('task.editTemplates')}}" title="{{ trans('task.editTemplates') }}">
                <span class="btn-img svgicon">
                    @include('macros.svg-icons.settings')
                </span>
            </a>
        </li>
    </ul>
</div>

<div class="main-container">
    <div id="nav_skipped" class="main-scroller card-body">
        {!! Form::open() !!}
            <div class="form-group">
                <label>Sélectionnez un template</label>
                <div class="input-group">
                    {!! Form::select('templates', $templates, null, ['class' => 'form-control templates']) !!}&nbsp;
                    <a href="{{ url()->route('task.addTemplate') }}" data-toggle="modal" data-target="#modal-ajax" style="line-height: 40px">Nouveau</a>
                </div>
                <div class="text-center">
                    <a href="{{ url()->route('task.editTemplate',array('edit'=>false,'template'=>'idtemplate')) }}" data-toggle="modal" data-target="#modal-ajax" class="visualize">Visualiser</a>
                </div>
            </div>
            <div class="form-group">
                <label>Nom</label>
                <div class="input-group">
                    <input class="form-control @if ($errors->has('project_name')) is-invalid @endif" name="project_name" value="">
                    @if ($errors->has('project_name'))
                        <span class="invalid-feedback">{{ $errors->first('project_name') }}</span>
                    @endif
                </div>
            </div>
            <div class="appends">
            </div>
            <div class="form-group">
                <div class="input-group">
                    <button class="button primary float-right">{{ trans('form.save') }}</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('sidebar')
    @include('components.sidebar-user')
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function(){
    change()
    $(document).on('click','.add',function(){
        $('.rows').append('@include("task.add-template-row1",["name"=>"","type"=>""])')
        $(this).addClass('remove').removeClass('add').html('-')
    })
    $(document).on('click','.remove',function(){
        $(this).parent().parent().remove()
    })
    $('.templates').on('change', function(){
        change()
    })
})
function change(){
    var link = "{{ url()->route('task.editTemplate',array('edit'=>false,'template'=>'idtemplate')) }}"
    var id = $('.templates').val()
    var newLink = link.replace(/idtemplate/, id)
    $('.visualize').attr('href',newLink)
    // var jqXhr = $.post("{{route('task.getCols')}}" , {
    //     postData : id
    // })
    // jqXhr.success(function(data) {
    //     $('.appends').html(data.body);
    // });
    return false;
}
</script>
@endsection