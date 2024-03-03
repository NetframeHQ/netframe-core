@extends('task.layout')

@section('subcontent')
<div class="main-container no-side">
  <div id="nav_skipped" class="main-scroller">
    <div class="documents">
      <div class="netframe-list-wrapper">
        <ul class="netframe-list list-unstyled">
          @if($hasValidation)
              <li class="item">
                  <a href="{{ route('task.validation') }}" class="nf-invisiblink"></a>
                  <div class="item-icon">
                      <span class="svgicon">
                          @include('macros.svg-icons.user_big')
                      </span>
                  </div>
                  <div class="document-infos">
                      <h4 class="document-title">
                          {{trans('task.validation')}}
                      </h4>
                  </div>
              </li>
          @endif

          @foreach($tasks as $task)
            <li class="item" id="task-{{ $task['task']->id }}">
              <a href="{{ route('task.project',array('project' => $task['task']->id)) }}" class="nf-invisiblink"></a>
              <div class="item-icon">
                <span class="svgicon">
                  <!-- @include('macros.svg-icons.'.$task['profileType'].'_big') -->
                  @include('macros.svg-icons.tasks')
                </span>
              </div>
              <div class="document-infos">
                <h4 class="document-title">
                  {{ $task['task']->name }}
                </h4>
                @if($task['profileType'] != 'user')
                  <p class="document-date">
                    {{--
                      @if($task->profile->profileImage)
                        <span class="avatar">
                          {!! HTML::thumbnail($task->profileImage, '40', '40', array('class' => 'float-left'), asset('assets/img/avatar/'.$task->getType().'.jpg')) !!}
                        </span>
                      @else
                        <span class="svgicon icon-tasks">
                          @include('macros.svg-icons.'.$task['profileType'].'_big')
                        </span>
                      @endif
                    --}}
                    <span class="task-name-profile">
                      {{ $task['profileName'] }}
                    </span>
                  </p>
                @endif
              </div>

              {{--
              <ul class="tesks-resume">
                  <li class="total">
                      <span class="svg-icon">
                          @include('macros.svg-icons.tasks')
                      </span>
                      {{ $task['task']->tasks()->count() }}
                  </li>
                  <li class="todo">
                      {{ $task['task']->todoTasks()->count() }}
                  </li>
                  <li class="in-progress">
                      <span class="svg-icon">
                          @include('macros.svg-icons.spinner')
                      </span>
                      {{ $task['task']->inProgressTasks()->count() }}
                  </li>
                  <li class="finished">
                      <span class="svg-icon">
                          @include('macros.svg-icons.check')
                      </span>
                      {{ $task['task']->finishedTasks()->count() }}
                  </li>
                  <li class="late">
                      {{ $task['task']->lateTasks()->count() }}
                  </li>
              </ul>
              --}}

              @if($task['role'] < 4)
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
                          <a class="nf-btn" href="{{ url()->route('task.editProject', array('projectId' => $task['task']->id)) }}">
                            <span class="btn-img svgicon">
                              @include('macros.svg-icons.edit')
                            </span>
                            <span class="btn-txt">
                              {{ trans('task.edit') }}
                            </span>
                          </a>
                        </li>
                        <li class="sep"></li>
                        @if($task['role'] < 3)
                          <li>
                            <a href="#" class="nf-btn delete-el" data-type="project" data-id="{{ $task['task']->id }}">
                              <span class="btn-img svgicon">
                                @include('macros.svg-icons.trash')
                              </span>
                              <span class="btn-txt">
                                {{ trans('task.delete') }}
                              </span>
                            </a>
                          </li>
                        @endif
                      </ul>
                    </div>
                  </li>
                </ul>
              @endif
              {{--
              <div class="right">
                <div class="users">
                  &nbsp;
                </div>
                <div class="deadline">
                  <ul class="list" style="float: right;">
                    <li>
                      <a class="alert-warning" href="{{ url()->route('task.editProject',array('projectId'=>$task['task']->id)) }}" title="{{ trans('task.util.edit') }}">
                        @include('macros.svg-icons.edit')
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              --}}
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="limit" id="limit" value="15">
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
            var panel = el.closest("li.item");

            var dataId = {type: el.data('type'), id: el.data('id') };

            var jqXhr = $.post("{{url()->route('task.delete')}}" , {
                postData : dataId
            });

            jqXhr.success(function(data) {
                if(data.delete){
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
  $('[data-toggle=tooltip]').tooltip()
})
</script>
@stop