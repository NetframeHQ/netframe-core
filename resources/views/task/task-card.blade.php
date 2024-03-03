{{-- <!-- ///// --> --}}
{{-- <!-- TBODY --> --}}
{{-- <!-- ///// --> --}}
<tr
  class="
    task
    @if($sub)
      sub
    @endif
    @if($task->archived || (isset($parent) &&$parent->archived))
      collapse
    @endif
  "
  @if($task->archived || (isset($parent) &&$parent->archived))
    id="archives"
  @endif
  data-archive="
    @if($sub)
      task-{{$parent->id}}
    @else
      task-{{$task->id}}
    @endif
  "

  @if(!$sub)
    data-main-task-id="task-{{$task->id}}"
  @endif
>
  {{-- <!-- INDEX OF TASK --> --}}
  <td>
    <div class="nf-task-cell">
      @if(!$sub)<span class="nf-lbl"><span class="lbl-txt">#{{$i}}</span></span>@endif
    </div>
  </td>

  {{-- <!-- NAME OF TASK --> --}}
  <td>
    <div
      class="nf-task-cell editable field"
      data-id="{{$task->id}}"
      data-field="name"
      data-type="text"
    ><span class="field-c">{{$task->name}}</span></div>
  </td>


  @if($project->template->linked)
    {{-- <!-- USER --> --}}
    <td class="user-col">
      <div class="nf-task-cell field"
        data-id="{{$task->id}}"
        data-text="{{ $task->workflow->user->id }}"
        data-field="users_id"
      >
        <div
          class="nf-btn btn-nobg user"
          title="{{ $task->workflow->user->getNameDisplay() }}"
        >
          {!! HTML::thumbImage(
              $task->workflow->user->profile_media_id,
              20,
              20,
              [],
              $task->workflow->user->getType(),
              'avatar',
              $task->workflow->user
          ) !!}
          <span class="btn-txt">
            {{ $task->workflow->user->getNameDisplay() }}
          </span>
        </div>
        <div class="task-user-search d-none">
          {{ Form::input('text', 'query', null, ['class' => 'form-control fn-search-contact-tasks', 'placeholder' => trans('channels.searchUsers'), 'autocomplete' => 'off']) }}
          <div class="display-users-results"></div>
        </div>
      </div>
    </td>


    {{-- <!-- STATUS --> --}}
    <td>
      <div
        class="nf-task-cell selectable field status-col"
        data-id="{{$task->id}}"
        data-field="finished"
      >
        @if($task->workflow->finished == 1)
          <span class="statut alert-success">
            {{trans('task.state.complete')}}
          </span>
        @elseif($task->workflow->finished==2)
          <span class="statut alert-danger">
            {{trans('task.state.todo')}}
          </span>
        @else
          <span class="statut alert-warning">
            {{trans('task.state.progress')}}
          </span>
        @endif
      </div>
    </td>

    {{-- <!-- DEADLINE --> --}}
    {{-- \App\Helpers\DateHelper::xplorerDate($task->deadline) --}}
    <td>
      <div
        class="nf-task-cell editable field"
        class=""
        data-id="{{$task->id}}"
        data-field="deadline"
        data-value="{{date_format(date_create($task->deadline),'d/m/Y')}}"
        data-type="date"
      >
        <span class="field-c">{{ date_format(date_create($task->deadline),'d/m/Y') }}{{-- moment().format("YYYY-MM-DD") --}}</span>
      </div>
    </td>
  @endif

  {{-- <!-- CUSTOM COLUMNS --> --}}
  @foreach($cols as $key => $col)

    {{-- <!-- CUSTOM TAG --> --}}
    @if($col['type']=='tag')
      @php
        $tags = $task->getCol($key);
      @endphp

      <td>
        <div class="nf-task-cell field">
          <span class="field-c">
            @if(is_array($tags) && count($tags)>0)
              <ul class="list-unstyled tags-list" id="userReferenceList">
                @foreach($tags as $tagId)
                  @php
                    $tag = \App\Tag::find($tagId);
                  @endphp
                  @if($tag)
                    <li>
                      <a href="{{ url()->route('tags.page', ['tagId' => $tag->id, 'tagName' => str_slug($tag->name)]) }}">
                        #{{ $tag->name }}
                      </a>
                    </li>
                  @endif
                @endforeach
              </ul>
            @endif
          </span>
        </div>
      </td>

    {{-- <!-- CUSTOM USER --> --}}
    @elseif($col['type']=='user')
      @php
        $users = $task->getCol($key);
      @endphp

      <td>
        <div class="nf-task-cell field user-col">
          <span class="field-c">
            @if(is_array($users) && count($users)>0)
              <ul class="list-unstyled profiles">
                @foreach($users as $userId)
                  @php
                    $user = \App\User::find($userId);
                  @endphp
                  @if($user)
                    <li>
                      <a class="user">
                        {!! HTML::thumbImage(
                            $user->profile_media_id,
                            20,
                            20,
                            [],
                            $user->getType(),
                            'avatar',
                            $user
                        ) !!}
                        {{ $user->getNameDisplay() }}
                      </a>
                    </li>
                  @endif
                @endforeach
              </ul>
            @endif
          </span>
        </div>
      </td>

    {{-- <!-- CUSTOM DATE / FILE --> --}}
    @else
      @php
        $val = $task->getCol($key);

        // manage editable cell (if media already inside, dont allow edit
        $editable = 'editable';
        if($col['type'] == 'file' && $task->getCol($key) != "") {
            $media = \App\Media::find($task->getCol($key));
            if ($media != null) {
                $editable = '';
            }
        }

      @endphp



      <td>
        <div class="nf-task-cell {{$editable}} field" data-custom="yes" data-id="{{$task->id}}" data-type="{{$col['type']}}" data-field="{{$key}}" data-value="{!!$val!!}">
          <span class="field-c {{$col['type']}}">
            @if($col['type'] == 'date' && $task->getCol($key) != "")
              @php
                $date = date("d/m/Y", strtotime($val));
                if($date=="01/01/1970")
                $date ="";
              @endphp
              {{$date}}
            @elseif($col['type'] == 'file' && $task->getCol($key) != "")
              <?php
              //$media = \App\Media::find($task->getCol($key));
              ?>
              @if($media != null)
                @if ($media->type == \Netframe\Media\Model\Media::TYPE_DOCUMENT || $media->type == \Netframe\Media\Model\Media::TYPE_ARCHIVE)
                  <a class="nf-btn" href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank">
                    <span class="svgicon btn-img">{!! \HTML::thumbnail($media, '100', '100', []) !!}</span>
                    <span class="btn-txt">{{ $media->name }}</span>
                  </a>
                @else
                  <a
                    href="#"
                    class="viewMedia"
                    data-media-name="{{ $media->name }}"
                    data-media-id="{{ $media->id }}"
                    data-media-type="{{ $media->type }}"
                    data-media-platform="{{ $media->platform }}"
                    data-media-mime-type="{{ $media->mime_type }}"

                    @if ($media->platform !== 'local')
                      data-media-file-name="{{ $media->file_name }}"
                    @endif
                  >
                    {!! \HTML::thumbnail($media, 100, 100, array('class' => 'img-thumbnail')) !!}
                  </a>
                @endif

                @if($media->mainProfile() != null)
                    @include('media.partials.menu-actions', [
                        'rights' => App\Http\Controllers\BaseController::hasRightsProfile($media->folder(), 5),
                        'profileType' => $media->mainProfile()->getType(),
                        'profileId' => $media->mainProfile()->id,
                        'openLocation' => true
                    ])
                @endif
              @else
                <div class="nf-btn" href="#">
                  <span class="svgicon btn-img">@include('macros.svg-icons.plus')</span>
                  <span class="btn-txt">{{trans('xplorer.plusMenu.importFile')}}</span>
                </div>
              @endif

            @else
              {!!$val!!}
            @endif
          </span>
        </div>
      </td>
    @endif
  @endforeach


  {{-- <!-- ACTIONS --> --}}
  <td>
    <div class="nf-task-cell field actions-col">
      <ul class="nf-actions">
        {{-- <!-- COMMENT  --> --}}
        <li class="nf-action">
          <a class="nf-btn btn-ico" href="{{route('task.comment', ['taskId'=>$task->id])}}" data-toggle="modal" data-target="#modal-ajax">
            <span class="svgicon btn-img">
              @include('macros.svg-icons.talk')
            </span>
            @if(!$sub && count($task->comments)>0)
              <span class="badge-comment">
                {{count($task->comments)}}
              </span>
            @endif
          </a>
        </li>
        {{-- <!-- ••• --> --}}
        @if((App\Http\Controllers\BaseController::hasRights($project) && App\Http\Controllers\BaseController::hasRights($project) < 4) || $task->users_id == auth('web')->user()->id)
          <li class="nf-action">
            <a href="#" class="nf-btn btn-ico btn-submenu">
              <span class="svgicon btn-img">
              @include('macros.svg-icons.menu')
              </span>
            </a>
            <div class="submenu-container submenu-right">
              <ul class="submenu">
                {{-- <!-- <li>
                  <a class="nf-btn" href="{{ route('task.editTask',['task' => $task->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                    <span class="btn-txt">
                      {{ trans('task.edit') }}
                    </span>
                  </a>
                </li> --> --}}
                @if(App\Http\Controllers\BaseController::hasRights($project) && App\Http\Controllers\BaseController::hasRights($project) < 4 && !$sub)
                  <li>
                    <a class="nf-btn" href="{{ route('task.link',['projectId'=>$project->id,'taskId' => $task->id]) }}" data-toggle="modal" data-target="#modal-ajax">
                      <span class="btn-txt">
                        {{ trans('task.link') }}
                      </span>
                    </a>
                  </li>
                @endif
                  <li>
                    <a href="#" class="nf-btn duplicate-el" data-id="{{ $task->id }}">
                      <span class="btn-txt">
                        {{ trans('task.duplicate') }}
                      </span>
                    </a>
                  </li>
                @if((App\Http\Controllers\BaseController::hasRights($project) && App\Http\Controllers\BaseController::hasRights($project) < 3)  || $task->users_id == auth('web')->user()->id)
                  <li>
                    <a href="#" class="nf-btn delete-el" data-type="task" data-id="{{ $task->id }}">
                      <span class="btn-txt">
                        {{ trans('task.delete') }}
                      </span>
                    </a>
                  </li>
                @endif
                @if(((App\Http\Controllers\BaseController::hasRights($project) && App\Http\Controllers\BaseController::hasRights($project) < 3)  || $task->users_id == auth('web')->user()->id) && !$sub && !$task->archived)
                  <li>
                    <a href="#" class="nf-btn archive-el" data-type="task" data-id="{{ $task->id }}">
                      <span class="btn-txt">
                        {{ trans('task.archive') }}
                      </span>
                    </a>
                  </li>
                @endif
              </ul>
            </div>
          </li>
        @endif
      </ul>
    </div>
  </td>

</tr>



{{-- <!-- CONTENT --> --}}

@if($task->workflow != null)
  @foreach($task->workflow->detailsActions as $action)
    <tr class="task sub">
      <td>
        <div class="nf-task-cell">
          &nbsp;
        </div>
      </td>
      <td class="field">
        <div class="nf-task-cell">
          {{ trans('workflow.actions.'.$action->actions->action_type) }}
        </div>
      </td>
      <td class="user field">
        <div class="nf-task-cell">
          @if($action->user != null)
            {!! HTML::thumbImage(
                $action->user->profile_media_id,
                20,
                20,
                [],
                $action->user->getType(),
                'avatar',
                $action->user
            ) !!}
            {{ $action->user->getNameDisplay() }}
          @endif
        </div>
      </td>
      <td class="field">
        <div class="nf-task-cell">
          @if($action->action_validate)
            <span class="statut alert-success">
              {{trans('task.state.complete')}}
            </span>
          @elseif($action->action_validate==2)
            <span class="statut alert-danger">
              {{trans('task.state.todo')}}
            </span>
          @else
            <span class="statut alert-warning">
              {{trans('task.state.progress')}}
            </span>
          @endif
        </div>
      </td>
      <td>
        @if($action->action_date != null)
            @if($action->action_date < date('Y-m-d'))
                <span class="statut alert-danger">
            @elseif($action->action_date > date('Y-m-d') && (((strtotime($action->action_date) - time()) / (60*60*24)) < 5 ))
                <span class="statut alert-warning">
            @else
                <span class="statut alert-info">
            @endif
                {{ date('d/m/Y', strtotime($action->action_date)) }}
            </span>
        @endif

        @if(!$action->action_validate && $action->users_id != null)
            <a href="#" data-action="{{$action->id}}" class="statut bg-light fn-revive-validation">{{trans('task.revive')}}</a>
        @else
            &nbsp;
        @endif

        @if ($action->actions->action_type == 'destination_folder')
            @if($action->destinationFolderProfile() != null)
                <div class="nf-task-cell">
                    {!! HTML::thumbImage(
                        $action->destinationFolderProfile()->profileImage,
                        20,
                        20,
                        [],
                        $action->destinationFolderProfile()->getType(),
                        'avatar',
                        $action->destinationFolderProfile()
                    ) !!}
                    {{ $action->destinationFolderProfile()->getNameDisplay() }}
                    <span class="svgicon">
                        @include('macros.svg-icons.arrow-right')
                    </span>
                    <span class="svgicon">
                        @include('macros.svg-icons.doc')
                    </span>
                    {{ $action->destinationFolder()->getNameDisplay() }}
                </div>
            @endif
        @endif
      </td>
      <td class="field" colspan="{{$nbCols-3}}">
        <div class="nf-task-cell">
          &nbsp;
        </div>
      </td>
    </tr>
  @endforeach
@endif
