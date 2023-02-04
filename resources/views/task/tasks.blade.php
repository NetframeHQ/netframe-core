@extends('task.layout')

@section('subcontent')
<div class="main-container">
  <div id="nav_skipped" class="main-scroller">
    <div class="search">
      <div style="min-height: 50px">
        <a href="{{ route('task.detailsProject',['projectId' => $project->id]) }}" class="button primary">
          {{ trans('task.details') }}
        </a>
        <a style="float: right;" href="{{ route('task.addTask',['project' => $project->id]) }}" data-toggle="modal" data-target="#modal-ajax" class="button primary">
          {{ trans('task.task.title') }}
        </a>
      </div>
    </div>
    <div class="bloc">
      <div class="tasks">
        @foreach($tasks as $task)
          <div class="task row">
            <div class="statut col-1">
              <span class="svgicon icon-talkgroup">
                @if($task->workflow->finished)
                @include('macros.svg-icons.check')
                @else
                      @include('macros.svg-icons.close')
                      @endif
                  </span>
            </div>
            <div class="name col-5">
              <!-- <a href="{{ route('task.project',array('project'=>$project->id)) }}"> -->{{ $task->name }}<!-- </a> -->
            </div>
            <div class="right col-5">
              <div class="users">
                <a data-toggle="tooltip" title="{{ $task->workflow->user->getNameDisplay() }}">
                @if($task->workflow->user->profileImage != null)
                  {!! HTML::thumbImage($task->workflow->user->profileImage, 80, 80, [], $task->workflow->user->getType(), 'avatar') !!}
                    @else
                        <span class="svgicon">
                            @include('macros.svg-icons.user')
                        </span>
                    @endif
                </a>
              </div>
              <div class="deadline">
                {{ \App\Helpers\DateHelper::xplorerDate($task->deadline) }}
              </div>
            </div>
            <div class="menu-wrapper col-1">
              <a class="fn-menu">
                <span class="svgicon icon-menu">
                  @include('macros.svg-icons.menu')
                </span>
              </a>
              <ul class="list-unstyled submenu-list submenu-noico float-left">
                <li>
                  <a href="{{ route('task.editTask',['task' => $task->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                    {{ trans('task.edit') }}
                  </a>
                </li>
                <li>
                  <a href="#" class="delete-el" data-type="task" data-id="{{ $task->id }}">
                    {{ trans('task.delete') }}
                  </a>
                </li>
              </ul>
            </div>
          </div>

          @foreach($task->childs as $sub)
            <div class="task row sub @if(request()->get('parent')!=null && request()->get('parent')==$task->id) @else nf-hidden @endif">
              <div class="statut col-1">
                @if($sub->workflow->finished)
                  @include('macros.svg-icons.check')
                @else
                  @include('macros.svg-icons.close')
                @endif
              </div>
              <div class="name col-5">
                {{ $sub->name }}
              </div>
              <div class="right col-5">
                <div class="users">
                  <a data-toggle="tooltip" title="{{ $sub->workflow->user->getNameDisplay() }}">
                    @if($sub->workflow->user->profileImage != null)
                      {!! HTML::thumbImage($sub->workflow->user->profileImage, 80, 80, [], $sub->workflow->user->getType(), 'avatar') !!}
                    @else
                      <span class="svgicon">
                        @include('macros.svg-icons.user')
                      </span>
                    @endif
                  </a>
                </div>
                <div class="deadline">
                  {{ \App\Helpers\DateHelper::xplorerDate($sub->deadline) }}
                </div>
              </div>
              <div class="menu-wrapper col-1">
                <a class="fn-menu">
                  <span class="svgicon icon-menu">
                    @include('macros.svg-icons.menu')
                  </span>
                </a>
                <ul class="list-unstyled submenu-list submenu-noico float-left">
                  <li>
                    <a href="{{ route('task.editTask',['task' => $sub->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                      {{ trans('task.edit') }}
                    </a>
                  </li>
                  <li>
                    <a href="#" class="delete-el" data-type="task" data-id="{{ $sub->id }}">
                      {{ trans('task.delete') }}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          @endforeach
        @endforeach
        </div>
      </div>
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
    if(!$(e.target).is('a.menu') || !$(e.target).parents('a.menu').length ){
      $(this).nextUntil('.task:not(.sub)').toggleClass('nf-hidden')
    }
  })
  $('[data-toggle=tooltip]').tooltip()

  $(document).ajaxSuccess(function(e,xhr){
    var data = JSON.parse(xhr.responseText)
      if(data['closeModal'])
        location.reload()
    })

    $('#modal-ajax').on('shown.bs.modal', function(){
      sel2()
    })
})
function sel2(){
  $('.select-user').select2({
        dropdownParent: $("#modal-ajax"),
        placeholder: "Saisir un user",
        // minimumInputLength: 1,
        templateResult: format,
        templateSelection: format,
        ajax: {
            url: "{{route('task.users')}}",
            dataType: "json",
            type: "POST",
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data, page) {
                return data;
            },
        },
        escapeMarkup: function(m) { return m; },
    });
}
function format(state) {
    // alert(JSON.stringify(state))
    if (!state.image) return state.text;
    //var originalOption = state.element;
    return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
}
</script>
@stop
