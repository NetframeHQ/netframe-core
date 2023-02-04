<div class="row">
    <div class="form-group @if(!($edit??true)) col-6 @else col-5 @endif">
        <div class="input-group">
            <input type="text" name="names[]" value="{{$name}}" @if(!($edit??true)) disabled="true" @endif class="form-control">
        </div>
    </div>
    <div class="form-group @if(!($edit??true)) col-5 @else col-4 @endif">
        <div class="input-group">
            <select name="types[]" class="form-control" @if(!($edit??true)) disabled="true" @endif>
                <option @if($type=='text')selected @endif value="text">{{ trans("task.template.text") }}</option>
                <option @if($type=='email')selected @endif value="email">{{ trans("task.template.email") }}</option>
                <option @if($type=='date')selected @endif value="date">{{ trans("task.template.date") }}</option>
                <option @if($type=='number')selected @endif value="number">{{ trans("task.template.number") }}</option>
                {{--<option @if($type=='tag')selected @endif value="tag">{{ trans("task.template.tag") }}</option>
                <option @if($type=='user')selected @endif value="user">{{ trans("task.template.user") }}</option>--}}
                <option @if($type=='file')selected @endif value="file">{{ trans("task.template.file") }}</option>
            </select>
        </div>
    </div>
    <div class="form-group col-1">
        <div class="input-group">
            <input type="checkbox" style="margin: 15px" @if(!($edit??true)) disabled="true" @endif name="required[]" class="form-control" checked>
        </div>
    </div>
    @if($edit??true)
    <div class="input-group col-2">
        <button class="btn btn-primary add" style="height: 40px" type="reset" title="{{ trans('task.template.add') }}">
            +
        </button>
    </div>
    @endif
</div>