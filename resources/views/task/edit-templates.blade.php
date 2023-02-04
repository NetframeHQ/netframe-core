@extends('layouts.master-header')

@section('title')
  {{ trans('task.editTemplates') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
  <div class="main-header-infos">
      <span class="svgicon icon-tasks">
          @include('macros.svg-icons.settings_big')
      </span>
      <h2 class="main-header-title">
        <a href="{{route('task.home')}}" title="{{ trans('task.title') }}">
          {{ trans('task.title') }}
        </a>
      </h2>
  </div>

  <ul class="nf-actions">
    <li class="nf-action">
      <a class="nf-btn" data-toggle="modal" data-target="#modal-ajax" href="{{ url()->route('task.addTemplate') }}">
        <span class="svgicon btn-img">
          @include('macros.svg-icons.plus')
        </span>
        <span class="btn-txt">
          {{ trans('task.createTemplate') }}
        </span>
      </a>
    </li>

    <li class="nf-action">
      <a class="nf-btn btn-ico" href="{{route('task.home')}}" title="{{ trans('task.title') }}">
        <span class="svgicon btn-img">
          @include('macros.svg-icons.back')
        </span>
      </a>
    </li>
  </ul>
@endsection

@section('content')
  <div id="nav_skipped" class="main-scroller">
    <div class="documents tasks">
      <div class="documents-breadcrumbs">
        <div class="breadcrumbs">
          <a
            href="{{route('task.home')}}"
            title="{{ trans('task.title') }}"
          >
            <span class="svgicon">
              @include('macros.svg-icons.tasks')
            </span>
          </a>
          <span class="breadcrumbs-item">
            <span class="svgicon icon-arrowdown">
              @include('macros.svg-icons.arrow-down')
            </span>
            <a href="#">
              {{ trans('task.editTemplates') }}
            </a>
          </span>
        </div>
      </div>


      @foreach($templates as $template)
        <div class="task">
          <a class="nf-invisiblink" href="{{ url()->route('task.editTemplate',['template'=>$template->id]) }}" data-toggle="modal" data-target="#modal-ajax"></a>
          <div class="task-icon">
            <span class="svgicon">
              @include('macros.svg-icons.settings')
            </span>
          </div>

          <div class="task-name">
            <h4 class="task-name-title">
              {{ $template->name }}
            </h4>
          </div>

          <div class="statut">
            <span class="svgicon icon-talkgroup"></span>
          </div>

          <ul class="nf-actions">
            <li class="nf-action">
              <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                  @include('macros.svg-icons.menu')
                </span>
              </a>
              <div class="submenu-container submenu-right">
                <ul class="submenu">
                  <li>
                    <a class="nf-btn" href="{{ url()->route('task.editTemplate', array('template'=>$template->id)) }}" data-toggle="modal" data-target="#modal-ajax">
                      <span class="btn-img svgicon">
                        @include('macros.svg-icons.edit')
                      </span>
                      <span class="btn-txt">
                        {{ trans('task.edit') }}
                      </span>
                    </a>
                  </li>
                  <!-- <li>
                    <a href="#" class="nf-btn delete-el" data-type="project" data-id="{{ $template->id }}">
                      <span class="btn-img svgicon">
                        @include('macros.svg-icons.trash')
                      </span>
                      <span class="btn-txt">
                        {{ trans('task.delete') }}
                      </span>
                    </a>
                  </li> -->
                </ul>
              </div>
            </li>
          </ul>
          <!-- <div class="right">
            <div class="users">
              &nbsp;
            </div>
            <div class="deadline">
              <ul class="list" style="float: right;">
                <li>
                  <a class="alert-warning" href="{{ url()->route('task.editProject',array('projectId'=>$template->id)) }}" title="{{ trans('task.util.edit') }}">
                    @include('macros.svg-icons.edit')
                  </a>
                </li>
                <li>
                </li>
              </ul>
            </div>
          </div> -->
        </div>
      @endforeach
    </div>
  </div>
  <input type="hidden" name="limit" id="limit" value="15">
@stop

@section('javascripts')
<script>
$(document).ready(function(){
  $(document).on('change', '#switch', function(){
    $('.defaults').toggleClass('d-none');
  });
  $(document).on('click', '.delete-el', function(e) {
        var _confirm = confirm('{{ trans('task.confirmDelete') }}');

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
        else{
            e.preventDefault();
            var el = $(this);
            var panel = el.closest(".task");

            var dataId = {type: el.data('type'), id: el.data('id') };

            var jqXhr = $.post("{{url()->route('task.delete')}}" , {
                postData : dataId
            });

            jqXhr.success(function(data) {
                if(dataId){
                    panel.fadeOut();
                }
            });
        }
        return false;
    });
  $('.task:not(.sub)').on('click', function(e){
    if(!$(e.target).is('a')){
      $(this).nextUntil('.task:not(.sub)').toggleClass('hide')
    }
  })
  //reload on closeModal
  $('#modal-ajax').on('hidden.bs.modal', function () {
      location.reload()
    });
    $(document).ajaxSuccess(function(events, xhr, settings) {
        if(xhr.responseText.includes("errors")){
            $('.templateName').addClass("is-invalid")
            $('.templateName').parent().append("<span class='invalid-feedback'>{{trans('task.requiredName')}}</span>");
        }
    })

    $(document).on('click','.add',function(){
        $('.rows').append('@include("task.add-template-row1",["name"=>"","type"=>""])')
        $(this).addClass('remove').removeClass('add').html('-')
        return false
    })
})
</script>
@stop