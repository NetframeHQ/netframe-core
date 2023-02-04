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
			<a class="nf-btn" href="{{route('task.editTemplates')}}">
				<span class="btn-img svgicon">
					@include('macros.svg-icons.settings')
				</span>
				<span class="btn-txt">
					{{ trans('task.editTemplates') }}
				</span>
			</a>
		</li>
  </ul>
</div>

<div class="main-container">
    <div id="nav_skipped" class="main-scroller">
        <div class="search">
        	<div style="min-height: 50px">
        		<a href="{{ route('task.editProject',['projectId' => $project->id]) }}" class="button primary">{{ trans('task.details') }}</a>
        		<a style="float: right;" href="{{ route('task.addTask',['project' => $project->id]) }}" class="button primary">{{ trans('task.task.title') }}</a>
        	</div>
		</div>
				<div class="bloc">
					<!-- <div class="title">
						<span class="text">Terminées</span>
						<span class="badge badge-primary">7</span>
						<a class="show"><i class="icon ion-md-arrow-dropdown"></i></a>
					</div> -->
					<div class="tasks">
						@foreach($tasks as $task)
						<div class="task row">
							<div class="statut">
								<span class="svgicon icon-talkgroup">
									@if($task->workflow->finished)
									@include('macros.svg-icons.check')
									@else
						            @include('macros.svg-icons.close')
						            @endif
						        </span>
							</div>
							<div class="name col-11">
								<!-- <a href="{{ route('task.project',array('project'=>$project->id)) }}"> -->{{ $task->name }}<!-- </a> -->
							</div>
							<div class="right">
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
						</div>
						@foreach($task->childs as $sub)
							<div class="task row sub nf-hidden">
								<div class="statut">
									@if($sub->workflow->finished)
									@include('macros.svg-icons.check')
									@else
						            @include('macros.svg-icons.close')
						            @endif
								</div>
								<div class="name col-10">
									{{ $sub->name }}
								</div>
								<div class="right">
									<div class="users">
										<a data-toggle="tooltip" title="{{ $task->workflow->user->getNameDisplay() }}">
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
							</div>
						@endforeach
						@endforeach
					</div>
				</div>
    </div>
</div>
<input type="hidden" name="limit" id="limit" value="15">
@stop

@section('sidebar')
    @include('components.sidebar-user')
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function(){
	$('.task:not(.sub)').on('click', function(e){
		if(!$(e.target).is('a')){
			$(this).nextUntil('.task:not(.sub)').toggleClass('nf-hidden')
		}
	})
	$('[data-toggle=tooltip]').tooltip()
})
</script>
@stop
