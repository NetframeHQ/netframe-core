<div class="modal-header">
    <h4 class="modal-title">
        @if(!($edit??true)) {{ trans('task.template.visual') }} @else {{ trans('task.template.edit') }} @endif
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>


<div class="modal-body">
    @if($template)
        {!! Form::open() !!}
        <div class="nf-form">
            <label class="nf-form-cell nf-cell-full @if($errors->has('name')) nf-cell-error @endif">
                <input type="text" name="name" @if(!($edit??true)) disabled="true" @endif value="{{$template->name}}" class="nf-form-input">
                <span class="nf-form-label">
                    {{ trans('task.template.templateName') }}
                </span>
                {!! $errors->first('name', '<p class="invalid-feedback">:message</p>') !!}
                <div class="nf-form-cell-fx"></div>
            </label>
        </div>
        @if($template->linked)
            {{-- used if workflow activated --}}
            <div class="defaults row nf-hidden">
                <div class="form-group col-5">
                    <div class="input-group">
                        <input type="text" disabled value="{{trans('task.header.name')}}" class="form-control">
                    </div>
                </div>
                <div class="form-group col-4">
                    <div class="input-group">
                        <select disabled class="form-control">
                            <option value="text">{{ trans("task.template.text") }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-1">
                    <div class="input-group">
                        <input type="checkbox" disabled style="margin: 15px" class="form-control" checked>
                    </div>
                </div>
                <div class="form-group col-5">
                    <div class="input-group">
                        <input type="text" disabled value="{{trans('task.header.user')}}" class="form-control">
                    </div>
                </div>
                <div class="form-group col-4">
                    <div class="input-group">
                        <select disabled class="form-control">
                            <option value="user">{{ trans("task.template.user") }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-1">
                    <div class="input-group">
                        <input type="checkbox" disabled style="margin: 15px" class="form-control" checked>
                    </div>
                </div>
                <div class="form-group col-5">
                    <div class="input-group">
                        <input type="text" disabled value="{{trans('task.header.state')}}" class="form-control">
                    </div>
                </div>
                <div class="form-group col-4">
                    <div class="input-group">
                        <select class="form-control" disabled>
                            <option value="text">{{ trans("task.template.boolean") }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-1">
                    <div class="input-group">
                        <input type="checkbox" disabled style="margin: 15px" class="form-control" checked>
                    </div>
                </div>
                <div class="form-group col-5">
                    <div class="input-group">
                        <input type="text" disabled value="{{trans('task.header.deadline')}}" class="form-control">
                    </div>
                </div>
                <div class="form-group col-4">
                    <div class="input-group">
                        <select disabled class="form-control">
                            <option value="date">{{ trans("task.template.date") }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-1">
                    <div class="input-group">
                        <input type="checkbox" disabled style="margin: 15px" class="form-control" checked>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="@if(!($edit??true)) col-6 @else col-5 @endif old-label">{{ trans('task.template.name') }}</div>
            <div class="@if(!($edit??true)) col-6 @else col-5 @endif old-label">{{ trans('task.template.type') }}</div>
        </div>
        <div class="rows old-nf-form">
            @if(count(json_decode($template->cols,true)) > 0)
                @foreach(json_decode($template->cols,true) as $key=>$value)
                    @include('task.add-template-row', ['name'=>$value['name'],'type'=>$value['type']])
                @endforeach
            @else
                @include('task.add-template-row',['name'=>'','type'=>''])
            @endif
        </div>
        @if($edit)
            <div class="nf-form-validation text-right">
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('form.save') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
        @endif

        {!! Form::close() !!}
    @else
        <div class="text-center">{{trans('task.none')}}</div>
    @endif
</div>

