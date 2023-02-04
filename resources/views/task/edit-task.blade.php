@extends('layouts.fullpage')

@section('content')
    <div class="card">
        <div class="card-header text-center">
            <a href="{{ session('landingDrivePage') }}" class="float-right">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('form.close') }}</span>
            </a>
            <h4>
                {{ trans('task.task.edit') }}
            </h4>
        </div>

<div class="main-container">
    <div id="nav_skipped" class="main-scroller search">
        <div class="tasks">
            {!! Form::open() !!}
            <div class="row">
                <div class="form-group col-12">
                    <label>{{ trans('task.task.name') }}</label>
                    <div class="input-group">
                        <input type="text" name="task_name" required value="{{$task->name}}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label>{{ trans('task.task.user') }}</label>
                    <div class="input-group">
                        <select name="task_user" class="select-user form-control">
                            <option value="{{$task->workflow->user->id}}">{{ucfirst($task->workflow->user->firstname)}} {{ucfirst($task->workflow->user->name)}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label>{{ trans('task.task.deadline') }}</label>
                    <div class="input-group">
                        <input type="date" name="deadline" value="{{ date('Y-m-d', strtotime($task->deadline)) }}" required class="form-control" min="{{-- date('Y-m-d') --}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label>{{ trans('task.task.parent') }}</label>
                    <div class="input-group">
                        @if($tasks!=[])
                        {!! Form::select('parent', [null=>trans('task.task.select')]+$tasks->toArray(), $task->parent, ['class' => 'form-control']) !!}&nbsp;
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12">
                    <label>{{ trans('task.task.state') }}</label>
                    <div class="input-group">
                        <select name="state" class="form-control">
                            <option value="0">{{ trans('task.task.progress') }}</option>
                            <option value="1" @if($task->workflow->finished) selected @endif>{{ trans('task.task.complete') }}</option>
                        </select>
                        <!-- <input type="checkbox" name="state" @if($task->workflow->finished) checked @endif> -->
                    </div>
                </div>
            </div>
            @if(is_array($cols))
                @foreach($cols as $key => $col)
                    <div class="row">
                        <div class="form-group col-12">
                            <label>{{ $col['name'] }}</label>
                            <div class="input-group">
                                @if($col['type']=='tag')
                                    <select name="cols[{{$key}}][]" multiple class="select-tag form-control">
                                        @php
                                            $tags = $task->getCol($key);
                                        @endphp
                                        @if(is_array($tags))
                                            @foreach($tags as $tagId)
                                                @php
                                                    $tag = \App\Tag::find($tagId);
                                                    if(!$tag)
                                                        $tag = \App\Tag::where('name',$tagId)->first();
                                                @endphp
                                                @if($tag)
                                                    <option selected="selected" value="{{$tag->id}}">{{$tag->name}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($col['type']=='user')
                                    <select name="cols[{{$key}}][]" multiple class="select-user form-control">
                                        @php
                                            $users = $task->getCol($key);
                                        @endphp
                                        @if(is_array($users))
                                            @foreach($users as $userId)
                                                @php
                                                    $user = \App\User::find($userId);
                                                @endphp
                                                @if($user)
                                                    <option selected="selected" value="{{$user->id}}">{{$user->getNameDisplay()}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                @else
                                <input name="cols[{{$key}}]" type="{{$col['type']}}" class="form-control">
                                @endif
                                <!-- <input type="checkbox" name="state" @if($task->workflow->finished) checked @endif> -->
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            <div class="row">
                <div class="form-group col-12">
                    <div class="input-group">
                        <button class="button primary float-right">{{ trans('form.save') }}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function(){
    $('.select-user').select2({
        // dropdownParent: $("#modal-ajax"),
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
    // $(document).ajaxSuccess(function(){

    // })
})
function format(state) {
    // alert(JSON.stringify(state))
    if (!state.image) return state.text;
    //var originalOption = state.element;
    return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
}
</script>
@stop