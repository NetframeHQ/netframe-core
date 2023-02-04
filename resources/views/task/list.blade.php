@extends('layouts.master-header')

@section('title')
  {{ trans('task.title') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
  <div class="main-header-infos">
    {{--
    @if($profile->profileImage)
      <span class="avatar">
        {!! HTML::thumbnail($profile->profileImage, '40', '40', array('class' => 'float-left'), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
      </span>
    @else
    @endif
    --}}
    <span class="svgicon icon-tasks">
      @include('macros.svg-icons.tasks_big')
    </span>

    <div class="main-header-title">
      @if($project->confidentiality == 0)
        <span class="private svgicon" title="{{ trans('messages.private') }}">
            @include('macros.svg-icons.private')
        </span>
      @endif
      <h2>
        {{-- {{$project->name}} --}}
        <a
          href="{{$profile->getUrl()}}"
          @if($profile->getType() === 'house')
              title="{{ trans('house.backToHouse') }}"
          @elseif($profile->getType() === 'community')
              title="{{ trans('community.backToCommunity') }}"
          @elseif($profile->getType() === 'project')
              title="{{ trans('project.backToProject') }}"
          @else
              title="{{ trans('channels.backToChannel') }}"
          @endif
        >
          {{ $profile->getNameDisplay() }}
        </a>
      </h2>
    </div>
    @if($profile->getType() != 'user' && $profile->description != '')
      <div class="main-header-subtitle">
        <p>
          {!! \App\Helpers\StringHelper::collapsePostText($profile->description, 200) !!}
        </p>
      </div>
    @endif
  </div>

  <ul class="nf-actions">
    {{-- <!-- ACTIONS ADDS --> --}}
    @if($project->has_medias == 1 || App\Http\Controllers\BaseController::hasRights($project) && App\Http\Controllers\BaseController::hasRights($project) < 5)
      <li class="nf-action postrows">
        <a href="#" class="nf-btn btn-ico btn-submenu">
          <span class="svgicon btn-img">
          @include('macros.svg-icons.plus')
          </span>
        </a>
        <div class="submenu-container submenu-right">
          <ul class="submenu">
            {{-- <!-- ADD TASKS --> --}}
            <li>
              <a
                alt="{{ trans('task.task.title') }}"
                class="nf-btn add-row"
                data-project="{{$project->id}}"
              >
                <span class="btn-img svgicon">
                  @include('macros.svg-icons.plus')
                </span>
                <span class="btn-txt">
                  {{ trans('task.task.title') }}
                </span>
              </a>
            </li>
            {{-- <!-- ADD SUBTASKS --> --}}
            <li>
              <a
                alt="{{ trans('task.task.sub') }}"
                href="{{route('task.sub', ['projectId'=>$project->id])}}"
                data-toggle="modal"
                data-target="#modal-ajax"
                class="nf-btn"
              >
                <span class="btn-img svgicon">
                  @include('macros.svg-icons.plus')
                </span>
                <span class="btn-txt">
                {{ trans('task.task.sub') }}
                </span>
              </a>
            </li>
            {{-- <!-- ADD MEDIA --> --}}
            @if($project->has_medias == 1)
              <li class="sep"></li>
              <li>
                <a
                  alt="{{ trans('xplorer.file.add.title') }}"
                  href="{{ url()->route('xplorer_add_file', ['profileType' => $profile->getType(), 'profileId' => $profile->id, 'idFolder' => null, 'driveFolder' => null, 'forceWorkflow' => 1]) }}"
                  class="fn-add-file nf-btn"
                  data-toggle="modal"
                  data-target="#modal-files"
                >
                  <span class="btn-img svgicon">
                    @include('macros.svg-icons.attach')
                  </span>
                  <span class="btn-txt">
                  {{ trans('xplorer.file.add.title') }}
                  </span>
                </a>
              </li>
            @endif

          </ul>
        </div>
      </li>
    @endif

    {{-- <!-- FILTERS --> --}}
    <li class="nf-action">
      <a href="#" class="nf-btn btn-ico btn-submenu">
        <span class="svgicon btn-img">
        @include('macros.svg-icons.filters')
        </span>
      </a>
      <div class="submenu-container submenu-right">
        <ul class="submenu">
          <li>
            <a class="nf-btn" href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'task'] : ['project'=>$project->id,'order-by'=>'task', 'type'=>'desc'])}}">
              <span class="btn-img svgicon">
                @include('macros.svg-icons.filters')
              </span>
              <span class="btn-txt">
                {{trans('task.header.name')}}
              </span>
            </a>
          </li>
          @if($project->template->linked)
          <li>
            <a class="nf-btn" href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'user'] : ['project'=>$project->id,'order-by'=>'user', 'type'=>'desc'])}}">
              <span class="btn-img svgicon">
                @include('macros.svg-icons.filters')
              </span>
              <span class="btn-txt">
                {{trans('task.header.user')}}
              </span>
            </a>
          </li>
          <li>
            <a class="nf-btn" href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'status'] : ['project'=>$project->id,'order-by'=>'status', 'type'=>'desc'])}}">
              <span class="btn-img svgicon">
                @include('macros.svg-icons.filters')
              </span>
              <span class="btn-txt">
                {{trans('task.header.state')}}
              </span>
            </a>
          </li>
          <li>
            <a class="nf-btn" href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'deadline'] : ['project'=>$project->id,'order-by'=>'deadline', 'type'=>'desc'])}}">
              <span class="btn-img svgicon">
                @include('macros.svg-icons.filters')
              </span>
              <span class="btn-txt">
                {{trans('task.header.deadline')}}
              </span>
            </a>
          </li>
          @endif
        </ul>
      </div>
    </li>

    @if($profile->getType() != 'user')
      <li class="nf-action">
        <a href="{{ $profile->getUrl() }}" class="nf-btn" title="{{ $profile->getNameDisplay() }}">
          <span class="btn-img svgicon">
            @include('macros.svg-icons.back')
          </span>
        </a>
      </li>
    @endif

    @if($project['role'] < 4)
      <li class="nf-action">
        <a href="#" class="nf-btn btn-ico btn-submenu">
          <span class="svgicon btn-img">
            @include('macros.svg-icons.settings')
          </span>
        </a>
        <div class="submenu-container submenu-right">
          <ul class="submenu">
            <li>
              <a class="nf-btn" href="#" title="{{ trans('task.edit') }}">
                <span class="btn-img svgicon">
                  @include('macros.svg-icons.settings')
                </span>
                <span class="btn-txt">
                  {{ trans('netframe.myInstance') }}
                </span>
              </a>
            </li>
            <li>
              <a class="nf-btn" href="{{route('task.editTemplates')}}" title="{{ trans('task.editTemplates') }}">
                <span class="btn-img svgicon">
                  @include('macros.svg-icons.settings')
                </span>
                <span class="btn-txt">
                  {{ trans('task.editTemplates') }}
                </span>
              </a>
            </li>
            <li class="sep"></li>
            <li>
              <a href="#" class="nf-btn delete-el" data-type="project" data-id="#">
                <span class="btn-img svgicon">
                  @include('macros.svg-icons.trash')
                </span>
                <span class="btn-txt">
                  {{ trans('task.delete') }}
                </span>
              </a>
            </li>
          </ul>
        </div>
      </li>
    @endif
  </ul>
@endsection

@section('content')
  <div id="container-tasks" class="nf-table-container">

    <div class="documents-breadcrumbs">
      <div class="breadcrumbs">
        <a href="{{route('task.home')}}" title="{{ trans('task.title') }}">
          <span class="svgicon">
            @include('macros.svg-icons.tasks')
          </span>
        </a>

        <span class="breadcrumbs-item">
          <span class="svgicon icon-arrowdown">
            @include('macros.svg-icons.arrow-down')
          </span>
          <a href="#" title="{{$project->name}}">
            {{$project->name}}
          </a>
        </span>

      </div>
    </div>

    <div class="table-scroll">
      <table class="table nf-table">
        {{-- COLUMNS SIZES --}}
        <colgroup>
          <col span="1" style="max-width: 40px;">
          <col span="1" style="min-width: 200px;">
        </colgroup>

        {{-- TASKS TABLE HEAD --}}
        <thead>
          <tr>
            <th>&nbsp;</th>
            <th>
              <a
                href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'task'] : ['project'=>$project->id,'order-by'=>'task', 'type'=>'desc'])}}"
                title="{{trans('task.header.name')}}"
              >
                {{trans('task.header.name')}}
                @if(request()->has('order-by') && request()->get('order-by')=='task')
                  @if(request()->has('type'))
                    @include('macros.svg-icons.sort-up')
                  @else
                    @include('macros.svg-icons.sort-down')
                  @endif
                @endif
              </a>
            </th>
            @if($project->template->linked)
              <th>
                <a
                  href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'user'] : ['project'=>$project->id,'order-by'=>'user', 'type'=>'desc'])}}"
                  title="{{trans('task.header.user')}}"
                >
                  {{trans('task.header.user')}}
                  @if(request()->has('order-by') && request()->get('order-by')=='user')
                    @if(request()->has('type'))
                      @include('macros.svg-icons.sort-up')
                    @else
                      @include('macros.svg-icons.sort-down')
                    @endif
                  @endif
                </a>
              </th>
              <th>
                <a
                  href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'status'] : ['project'=>$project->id,'order-by'=>'status', 'type'=>'desc'])}}"
                  title="{{trans('task.header.state')}}"
                >
                  {{trans('task.header.state')}}
                  @if(request()->has('order-by') && request()->get('order-by')=='status')
                    @if(request()->has('type'))
                      @include('macros.svg-icons.sort-up')
                    @else
                      @include('macros.svg-icons.sort-down')
                    @endif
                  @endif
                </a>
              </th>
              <th>
                <a
                  href="{{route('task.project', (!request()->has('order-by') || request()->has('type')) ? ['project'=>$project->id,'order-by'=>'deadline'] : ['project'=>$project->id,'order-by'=>'deadline', 'type'=>'desc'])}}"
                  title="{{trans('task.header.deadline')}}"
                >
                  {{trans('task.header.deadline')}}
                  @if(request()->has('order-by') && request()->get('order-by')=='deadline')
                    @if(request()->has('type'))
                      @include('macros.svg-icons.sort-up')
                    @else
                      @include('macros.svg-icons.sort-down')
                    @endif
                  @endif
                </a>
              </th>
            @endif
            @foreach($cols as $key => $col)
              <th>{{ucfirst($col['name'])}}<br><span>{{lcfirst(trans('task.template.'.$col['type']))}}</span></th>
            @endforeach
            <th>&nbsp;</th>
          </tr>
        </thead>

        {{-- TASKS TABLE BODY --}}
        <tbody>
          @php
            $i = 1;
          @endphp
          @foreach($tasks as $task)
            @include('task.task-card',['task'=>$task, 'sub'=>false])
            @php
              $i++;
            @endphp
            @foreach($task->childs as $sub)
            @include('task.task-card',['task'=>$sub,'sub'=>true,'parent'=>$task])
            @endforeach

          @endforeach
          @php
            $cc = json_decode($project->template->cols, true);
          @endphp

          @if(count($archives)>0)
            @php
              $i = 1;
            @endphp
            <tr class="before">
              <td class="text-center" colspan="@if($project->template->linked) {{count($cc)+7}} @else {{count($cc)+3}} @endif">
                <a href="#" class="nf-btn" data-target=".collapse" data-toggle="collapse">
                  <span class="btn-img svgicon">
                    @include('macros.svg-icons.archive')
                  </span>
                  <span class="btn-txt">
                    {{trans('task.archives')}}
                  </span>
                  <span class="btn-txt btn-digit">
                    ({{count($archives)}})
                  </span>
                </a>
              </td>
            </tr>
            @foreach($archives as $task)
              @include('task.task-card',['task'=>$task,'sub'=>false])
              @php
                $i++;
              @endphp

              @foreach($task->childs as $sub)
                @include('task.task-card',['task'=>$sub,'sub'=>true,'parent'=>$task])
              @endforeach

            @endforeach
          @endif

          @if(App\Http\Controllers\BaseController::hasRights($project) && App\Http\Controllers\BaseController::hasRights($project) < 5)
            <tr class="postrows task">
              <td colspan="@if($project->template->linked) {{(count($cc)+7)/2}} @else {{(count($cc)+3)/2}} @endif">
                <a href="#" class="nf-btn add-row" data-project="{{$project->id}}">
                  <span class="btn-img svgicon">
                    @include('macros.svg-icons.plus')
                  </span>
                  <span class="btn-txt">
                    {{ trans('task.task.title') }}
                  </span>
                </a>
              </td>
              <td colspan="@if($project->template->linked) {{(count($cc)+7)/2}} @else {{(count($cc)+3)/2}} @endif">
                <a class="nf-btn" href="{{route('task.sub', ['projectId'=>$project->id])}}" data-toggle="modal" data-target="#modal-ajax">
                  <span class="btn-img svgicon">
                    @include('macros.svg-icons.plus')
                  </span>
                  <span class="btn-txt">
                    {{ trans('task.task.sub') }}
                  </span>
                </a>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
@stop

@section('javascripts')
@parent
<script>
var currentOpenProject = {{ $project->id }};
</script>
{{ HTML::script('packages/netframe/tasks/js/tasks.js?v=' . env('ASSETS_VERSION', rand())) }}
<script>
$(document).ready(function(){
  $('.table input[type="number"]').on('add', {
    digits: true,
    messages: {
      required: " Please enter a score!",
      digits: " Please only enter numbers!"
    }
  })

  // reload on closeModal
  /*
  $(document).ajaxSuccess(function(e,xhr){
    var data = JSON.parse(xhr.responseText)
    if(data['closeModal'])
      location.reload()
  })
  */

  $('#modal-ajax').on('shown.bs.modal', function(){
    sel2()
  })

  var wrapperTasksSystem = $('#container-tasks');
  var tasksSystem = new Tasks({
    $wrapper: wrapperTasksSystem,
    $projectId: {{ $project->id }}
  });
  tasksSystem.table();
})

function sel2($select){
  $('.select-user').select2({
    dropdownParent: $("#modal-ajax"),
    placeholder: "Saisir un user",
    // minimumInputLength: 1,
    templateResult: format,
    templateSelection: format,
    ajax: {
      url: laroute.route('task.users'),
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
  $('.select-tag').select2({
    placeHolder:'tapez ici',
    minimumInputLength: 2,
    multiple: true,
    maximumSelectionLength: 7,
    maximumSelectionSize:function(){
      return 1;
    },
    ajax: {
      url: laroute.route('tags.autocomplete'),
      dataType: 'json',
      contentType: "application/json",
      type: "POST",
      data: function (params) {
        return  JSON.stringify({
          q: params.term
        });
      },
      processResults: function (data, page) {
        return data;
      },
    },
    escapeMarkup: function (markup) { return markup; },
  });
}
function format(state) {
  // alert(JSON.stringify(state))
  if (!state.image) return state.text;
  //var originalOption = state.element;
  return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
}

(function($){
  //search contacts

  $(document).on('click', '.user-col', function(e){
    e.preventDefault();
    $(this).closest('td').find('.task-user-search').removeClass('d-none');
  });


  $(document).on('click', '.archive-el', function(e) {
    var _confirm = confirm(trans('task.confirmArchive'));

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
      var archive = panel.data("archive");
      var dataId = {id: el.data('id') };

      var jqXhr = $.post("{{route('task.archive')}}" , {
        postData : dataId
      });

      jqXhr.success(function(data) {
        if(dataId){
          // alert("tr[data-archive="+archive+"]")
          // $("tr[data-archive='"+archive+"']").before(".before")
          window.location.reload()
          // panel.fadeOut();
        }
      });
    }
    return false;
  });

  $(document).on('keyup', 'tr.task .fn-search-contact-tasks', function(e){
    var searchContainer = $(this).closest('.task-user-search');
    var input = $(this).val();
    if(input.length > 2){
      // start contacts and users search

      $.ajax({
        url: laroute.route('task.users'),
        type: "POST",
        data: {
          query: input
        },
        success: function(data) {
          // display div under search with user result list
          var source = $('#template-tasks-search-users').html();
          var template = Handlebars.compile(source);
          var html = template({users: data.users});
          searchContainer.find(".display-users-results").html(html);
        }
      });
    }
  });

  $(document).on('click', '.task-user-search .select-user', function(e){
    e.preventDefault();
    var col = $(this).closest('td.user-col');

    // do stuf to update task in ajax

    // copy user line
    $(this).removeClass('select-user');
    $(this).addClass('user');
    var userHtml = $(this).wrap('<p/>').parent().html();
    col.find('.nf-task-cell div.user').remove();
    col.find('.nf-task-cell').prepend(userHtml);
    //save data
    var dataId = {field: 'users_id', id: col.find('div.field').data('id'), value: col.find('div.user').data('user-id'), project: {{ $project->id }}};
    // alert(JSON.stringify(dataId));
    var jqXhr = $.post(laroute.route('task.addTaskCol') , {
         postData : dataId
    });
    // re-init form
    col.find('.fn-search-contact-tasks').val('');
    setTimeout(function(){
        col.find('.task-user-search').addClass('d-none');
        }, 200);
  });

  //for modal media view
  var $modal = $('#viewMediaModal');

  playMediaModal = new PlayMediaModal({
      $modal: $modal,
      $modalTitle: $modal.find('.modal-title'),
      $modalContent: $modal.find('.modal-carousel .carousel-item'),
      $media: $('.viewMedia'),
      baseUrl: baseUrl
  });

})(jQuery);
</script>
@include('task.user-result')
@stop