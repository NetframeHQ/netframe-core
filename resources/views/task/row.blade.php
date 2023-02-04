@php
    $i = $nbDirectTasks;
@endphp
@include('task.task-card',['task'=>$task, 'sub'=>false])
@php
    $i++;
@endphp
@foreach($task->childs as $sub)
    @include('task.task-card',['task'=>$sub,'sub'=>true,'parent'=>$task])
@endforeach
