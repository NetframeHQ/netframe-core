@extends('instances.main')

@section('title')
    {{ trans('instances.usersdata.title') }} • {{ $globalInstanceName }}
@stop

@section('subcontent')
    <div class="nf-form">
        <div class="nf-settings-title">
            {{ trans('instances.usersdata.title') }}
        </div>
        {{Form::open()}}
            @if(session()->has('usersdata'))
                <div class="nf-form-cell bg-success">
                    {{ trans('instances.usersdata.success') }}
                </div>
            @endif
            <ul class="nf-list-settings">
                @if($usersdata != null)
                    @foreach($usersdata as $key => $value)
                        <li class="nf-list-setting">

                            <div class="nf-list-input">
                                <input name="slugs[]" type="hidden" class="form-control" value="{{$key}}"> 
                                <input type="text" name="names[]" class="form-control" value="{{$value['name']}}" placeholder="{{ trans('instances.usersdata.dataLabel') }}"> 
                            </div>

                            <div class="nf-list-input">
                                <select name="inputs[]" class="form-control" value="" style="height: 100%">
                                    <option value="" disabled>{{ trans('instances.usersdata.dataType') }}</option>
                                    <option value="text" @if($value['type'] == 'text') selected @endif>{{ trans('instances.usersdata.inputType.input') }}</option>
                                    <option value="textarea" @if($value['type'] == 'textarea') selected @endif>{{ trans('instances.usersdata.inputType.textarea') }}</option>
                                    <option value="checkbox" @if($value['type'] == 'checkbox') selected @endif>{{ trans('instances.usersdata.inputType.checkbox') }}</option>
                                </select>
                            </div>

                            <ul class="nf-actions">
                                <li class="nf-action">
                                    <a href="#" class="nf-btn btn-ico">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.trash')
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                @endif
                <li class="nf-list-setting">

                    <div class="nf-list-input">
                        <input type="text" name="names[]" class="name form-control" placeholder="{{ trans('instances.usersdata.dataLabel') }}"> 
                    </div>

                    <div class="nf-list-input">
                    <select name="inputs[]" class="form-control type" style="height: 100%">
                            <option value="" disabled selected>{{ trans('instances.usersdata.dataType') }}</option>
                            <option value="text">{{ trans('instances.usersdata.inputType.input') }}</option>
                            <option value="textarea">{{ trans('instances.usersdata.inputType.textarea') }}</option>
                            <option value="checkbox">{{ trans('instances.usersdata.inputType.checkbox') }}</option>
                        </select>
                    </div>

                    <ul class="nf-actions">
                        <li class="nf-action">
                            <a href="#" class="nf-btn btn-ico">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.plus')
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="nf-form-validation">
                <!-- <div class="nf-form-validactions">
                    <ul class="nf-actions">
                        <li class="nf-action">
                            <a href="#" class="nf-btn btn-xxl">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.plus')
                                </span>
                                <span class="btn-txt">
                                    Add
                                </span>
                            </a>
                        </li>
                    </ul>
                </div> -->
                <button type="submit" class="nf-btn btn-primary btn-xxl">
                    <div class="btn-txt">
                        {{ trans('form.save') }}
                    </div>
                    <div class="svgicon btn-img">
                        @include('macros.svg-icons.arrow-right')
                    </div>
                </button>
            </div>
        {{Form::close()}}
    </div>
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function(){
    $('body').on('click', '.add button.btn', function(){
        var parent = $(this).parent();
        parent.parent().parent().append('<div class="row"><div class="input-group col-md-5"><input name="names[]" class="form-control"></div><div class="input-group col-md-5"><select name="inputs[]" class="form-control" style="height: 100%"><option value="input">{{ trans("instances.usersdata.inputType.input") }}</option><option value="textarea">{{ trans("instances.usersdata.inputType.textarea") }}</option><option value="checkbox">{{ trans("instances.usersdata.inputType.checkbox") }}</option></select></div><div class="input-group col-md-2 add"><button class="btn btn-primary" type="reset" title="{{ trans("instances.usersdata.add") }}"><span class="svgicon icon-plus">+</span></button></div><div class="col-md-12">&nbsp;</div></div>');
        $(this).html('<span class="svgicon icon-plus">-</span>');
        parent.removeClass('add').addClass('remove');
        return false;
    });
    $('body').on('click', '.remove button.btn', function(){
        var card = $(this).parent().parent().parent();
        $(this).parent().parent().slideUp(300, function(){
            $(this).remove();
        });
        return false;
    });
    $('body').on('click', '.deleted button.btn', function(){
        var jqXhr = $.post("{{route('instances.delete-custom')}}" , {
            postData : {
                type: $(this).data('type'),
                slug: $(this).data('slug')
            }
        });
        $(this).parent().parent().slideUp(300, function(){
            $(this).remove();
        });
        return false;
    });
});

</script>
@stop