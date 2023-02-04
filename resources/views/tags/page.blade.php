@extends('layouts.master')

@section('title')
    {{ trans('tags.tag') }} : {{ $tag->name }} • {{ $globalInstanceName }}
@stop

@section('customCssContent')
    h-padding-15
@stop

@section('content')

    <div class="panel">
        <div class="panel-heading">
            <h1>{{ trans('tags.tag') }} : {{ $tag->name }}</h1>
        </div>
    </div>

    @foreach($tagRelations as $relationType=>$tagRelation)
        @if($tagRelation->count() > 0)
            <h2>{{ trans('tags.relationTitles.'.$relationType) }}</h2>
            <div class="tags-page-group">
                @foreach($tagRelation as $element)
                    @if(in_array(class_basename($element), ['House', 'Community', 'Project']))
                        @include("tags.type-content.Profile")
                    @else
                        @include("tags.type-content.".class_basename($element))
                    @endif
                @endforeach
            </div>
        @endif
    @endforeach

@stop