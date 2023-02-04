<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('project.bookmarks') }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body">

    {{ Form::open(['route'=> ['project_bookmark_form', $project->id, $bookmark->id], 'id' => 'form-bookmark']) }}
    {{ Form::hidden("project_id", $project->id ) }}

    <div class="form-group">
        {{ Form::label('name', trans('project.bookmarkTitle')) }}
        {{ Form::text('name', $bookmark->name, ['class' => 'form-control '.(($errors->has('name')) ? 'is-invalid' : '')] ) }}

        @if ($errors->has('name'))
            <span class="invalid-feedback">{{ $errors->first('name') }}</span>
        @endif
    </div>

    <div class="form-group">
        {{ Form::label('url', trans('project.bookmarkUrl')) }}
        {{ Form::text('url', $bookmark->url, ['class' => 'form-control '.(($errors->has('url')) ? 'is-invalid' : '')] ) }}

        @if ($errors->has('url'))
            <span class="invalid-feedback">{{ $errors->first('url') }}</span>
        @endif
    </div>

    <div class="form-group">
        {{ Form::label('description', trans('project.bookmarkDescription')) }}
        {{ Form::textarea('description', $bookmark->description, ['rows'=>'7', 'class'=>'form-control '.(($errors->has('description')) ? 'is-invalid' : '')] ) }}

        @if ($errors->has('description'))
            <span class="invalid-feedback">{{ $errors->first('description') }}</span>
        @endif
    </div>

    <div class="form-group clearfix">
        <div class="float-right">
            <button type="button" class="button primary" data-dismiss="modal">{{ trans('form.close') }}</button>
            <button type="submit" class="button primary">
                @if($bookmark->id != null)
                    {{ trans('project.modify') }}
                @else
                    {{ trans('project.add') }}
                @endif
            </button>
        </div>

    </div>

    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->

@yield('javascriptModal')

