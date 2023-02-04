@extends('layouts.fullpage')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4>{{$task->name}}</h4>
            <br>
            <div class="comments col-12">
                @if($comments->count()>0)
                    @foreach($comments as $comment)
                        <div class="comment col-12 row">
                            <div class="col-md-1 col-3">
                                @if($comment->author->profileImage != null)
                                    {!! HTML::thumbImage($comment->author->profileImage, 45, 45, [], $comment->author->getType(), 'avatar') !!}
                                @else
                                    <span class="svgicon">
                                        @include('macros.svg-icons.user')
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-10 col-9">
                                <span class="author">{{$comment->author->getNameDisplay()}}</span>
                                <span class="date">{{ \App\Helpers\DateHelper::xplorerDate($comment->created_at, $comment->updated_at) }}</span>
                                <br>
                                {!!nl2br($comment->content)!!}
                            </div>
                        </div>
                    @endforeach
                @else
                    <h5>{{trans('task.comment.first')}}</h5>
                @endif
            </div>
            <br>
            {!! Form::open() !!}
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>{{ trans('task.comment.title') }}</label>
                        <div class="input-group">
                            <textarea name="comment" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group offset-10">
                        <div class="input-group" style="float: right;">
                            <button class="button primary">{{ trans('task.comment.title') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

