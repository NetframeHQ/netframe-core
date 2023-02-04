@extends('task.layout')

@section('title')
    {{ trans('task.createProject') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
  <div class="main-header-infos">
    <span class="svgicon icon-talkgroup">
      @include('macros.svg-icons.tasks_big')
    </span>
    <h2 class="main-header-title">
      {{ trans('task.createProject') }}
    </h2>
  </div>
  <ul class="nf-actions">
    <li class="nf-action">
      <a class="nf-btn btn-ico" href="{{route('task.home')}}" >
        <span class="svgicon btn-img">
          @include('macros.svg-icons.back')
        </span>
      </a>
    </li>
    <li class="nf-action">
        <button type="submit" form="createProject" class="nf-btn btn-primary">
            <div class="btn-txt">
                {{ trans('form.save') }}
            </div>
            <div class="svgicon btn-img">
                @include('macros.svg-icons.arrow-right')
            </div>
        </button>
    </li>
  </ul>
@endsection

@section('subcontent')

<div class="main-container no-side">
    <div id="nav_skipped" class="main-scroller">
        <div class="nf-settings nf-tasks-create">
            <div class="nf-settings-content">
                <div class="nf-form nf-col-2">
                    {!! Form::open(['id'=>'createProject', 'files'=>true]) !!}
                        <div id="publish-as-hidden-nw">
                            {{ Form::hidden("id_foreign", (isset($project->author_id) ? $project->author_id : auth()->guard('web')->user()->id) ) }}
                            {{ Form::hidden("type_foreign", (isset($project->author_type) ? strtolower(class_basename($project->author)) : 'user') ) }}
                        </div>
                        <div id="publish-as-hidden-nwas">
                            {{ Form::hidden("id_foreign_as", (isset($project->post->true_author_id) ? $project->post->true_author_id : auth()->guard('web')->user()->id) ) }}
                            {{ Form::hidden("type_foreign_as", (isset($project->post->true_author_type) ? strtolower(class_basename($project->post->true_author)) : 'user') ) }}
                        </div>

                        <label class="nf-form-cell nf-cell-full nf-cell-name @if($errors->has('name')) nf-cell-error @endif">
                            <input class="nf-form-input @if ($errors->has('project_name')) is-invalid @endif" name="project_name" placeholder="{{ trans('form.writeHere') }}" value="{{isset($project) ? $project->name:request()->old('project_name')}}">
                            <span class="nf-form-label">
                                {{ trans('task.name') }}
                            </span>
                            {!! $errors->first('project_name', '<p class="nf-form-feedback">:message</p>') !!}
                            <div class="nf-form-cell-fx"></div>
                        </label>
                        <div class="appends">
                        </div>

                        <!-- <hr> -->

                        <section class="nf-form-col">
                            <!-- SELECT TEMPLATES -->
                            <label class="nf-form-cell">
                                @include('task.partials.template-selector')
                                <span class="nf-form-label">
                                    {{ trans('task.selectTemplate') }}
                                </span>
                                <div class="nf-form-cell-fx"></div>
                            </label>

                            @if(session('instanceId') == 425)
                                <label class="nf-form-cell">
                                    {!! Form::select('premodel', ['0' => '', '1' => 'Modèle Gilead'], null, ['class' => 'form-control']) !!}
                                    <span class="nf-form-label">
                                        {{ trans('task.selectModel') }}
                                    </span>
                                    <div class="nf-form-cell-fx"></div>
                                </label>
                            @endif
                        </section>

                        <section class="nf-form-col">
                            <ul class="nf-actions">
                                <li class="nf-action">
                                    <a class="nf-btn visualize" href="{{ url()->route('task.editTemplate',array('edit'=>false,'template'=>'0')) }}" data-toggle="modal" data-target="#modal-ajax" class="visualize">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.file')
                                        </span>
                                        <span class="btn-txt">
                                            {{ trans('task.preview') }}
                                        </span>
                                    </a>
                                </li>
                                <li class="nf-action">
                                    <a class="nf-btn" href="{{ url()->route('task.addTemplate', ['projectId' => (isset($project)) ? $project->id : null]) }}"
                                        data-toggle="modal"
                                        data-target="#modal-ajax">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.plus')
                                        </span>
                                        <span class="btn-txt">
                                            {{ trans('task.new_template') }}
                                        </span>
                                    </a>
                                </li>

                                <!-- <li class="nf-action">
                                    <a class="nf-btn" href="{{ url()->route('task.deleteTemplate',array('template'=>'idtemplate')) }}" class="delete">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.trash')
                                        </span>
                                        <span class="btn-txt">
                                            {{ trans('task.delete') }}
                                        </span>
                                    </a>
                                </li> -->
                            </ul>



                        </section>

                        <div class="nf-form-validation">
                            <div class="nf-form-validactions">
                                <div class="nf-form-cell nf-cell">
                                    {!! HTML::publishAs('#createProject',
                                        $NetframeProfiles, [
                                            'id'=>'id_foreign',
                                            'type'=>'type_foreign',
                                            'postfix'=>'nw'
                                        ],
                                        true,
                                        (isset($project->author_id)) ? [
                                            'id' => $project->author_id,
                                            'type' => strtolower(class_basename($project->author))
                                            ] : [
                                                'id' => auth()->guard('web')->user()->id,
                                                'type' => 'user'
                                            ]
                                        )
                                    !!}
                                    <span class="nf-form-label">
                                        {{ trans('netframe.publishOn') }}
                                    </span>
                                </div>

                                <div class="nf-form-cell nf-cell tl-publish-as-choice">
                                    {!! HTML::publishAs('#createProject',
                                        $NetframeProfiles, [
                                            'id'=>'id_foreign_as',
                                            'type'=>'type_foreign_as',
                                            'postfix'=>'nwas',
                                            'secondary' => true
                                        ],
                                        true,
                                        (isset($project->post->true_author)) ? [
                                            'id' => $project->post->true_author->id,
                                            'type' => strtolower(class_basename($project->post->true_author))
                                            ]: [
                                                'id' => auth()->guard('web')->user()->id,
                                                'type' => 'user'
                                            ]
                                        )
                                    !!}
                                    <span class="nf-form-label">
                                        {{ trans('netframe.publishAs') }}
                                    </span>
                                </div>
                            </div>

                            <!-- <button type="submit" class="nf-btn btn-primary btn-xxl">
                                <div class="btn-txt">
                                    {{ trans('form.save') }}
                                </div>
                                <div class="svgicon btn-img">
                                    @include('macros.svg-icons.arrow-right')
                                </div>
                            </button> -->
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascripts')
@parent
<script>
$(document).ready(function(){
    $(document).on('click','.add',function(){
        $('.rows').append('@include("task.add-template-row1",["name"=>"","type"=>""])')
        $(this).addClass('remove').removeClass('add').html('-')
        return false
    })
    $(document).on('change', '#switch', function(){
        $('.defaults').toggleClass('d-none');
    })
    $(document).on('change', 'select[name="templates"]', function(){
        let value = $('.visualize').attr('href')
        let lastIndex = value.lastIndexOf('/')
        let id = this.value
        $('.visualize').attr('href', value.substring(0,lastIndex+1).concat(id+'?edit=0'))
        // ----- delete link
        value = $('.delete').attr('href')
        lastIndex = value.lastIndexOf('/')
        id = this.value
        $('.delete').attr('href', value.substring(0,lastIndex+1).concat(id))
    })
    $(document).on('click', '.delete', function(e) {
        var _confirm = confirm('{{ trans('task.confirmDelete') }}');

        if (!_confirm) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        }
        else{
            // e.preventDefault();
            var el = $(this);
            var panel = el.closest("li.task");

            var dataId = {type: el.data('type'), id: el.data('id') };
            var link = $('.delete').attr('href');

            var jqXhr = $.post(link , {
                postData : dataId
            });
            jqXhr.success(function(data) {
                if(data.deleted){
                    $(".templates option[value='"+data.id+"']").remove();
                }else{
                    alert("{{trans('task.cantDelete')}}")
                }
            });
        }
        return false;
    });
    $(document).on('click','.remove',function(){
        $(this).parent().parent().remove()
        return false
    })
    $(document).ajaxSuccess(function(events, xhr, settings) {
        if(xhr.responseText.includes("errors")){
            $('.templateName').addClass("is-invalid")
            $('.templateName').parent().append("<span class='invalid-feedback'>{{trans('task.requiredName')}}</span>");
        }
    })
})

function select_user(){
    $('.select-user').select2({
        placeholder: "Saisir un user",
        minimumInputLength: 1,
        multiple: true,
        maximumSelectionLength: 7,
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
}
function select_tag(){
    $('.select-tag').select2({
        placeHolder:'tapez ici',
        minimumInputLength: 2,
        multiple: true,
        maximumSelectionLength: 7,
        maximumSelectionSize:function(){
            return 1;
        },
        ajax: {
            url: "{{route('tags.autocomplete')}}",
            dataType: 'json',
            contentType: "application/json",
            type: "POST",
            data: function (params) {
                return  JSON.stringify({
                    q: params.term
                });
            },
            processResults: function (data, page) {
                return data;
            },
        },
        escapeMarkup: function (markup) { return markup; },
    });
}
function format(state) {
    // alert(JSON.stringify(state))
    if (!state.image) return state.text;
    //var originalOption = state.element;
    return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
}

(function () {
    var attachmentSystem = $('#medias'); // ici l’id container de la ligne de tableau task
    new AttachmentSystem({
        $wrapper: attachmentSystem,
        $fileUpload: attachmentSystem.find('#fileupload'),
        $profileMedia: 0,
        $postMedia: 1,
        $mediaTemplateRender: '.tl-posted-medias',
        $confidentiality: attachmentSystem.find('input:radio[name=confidentiality]'),
        $profileId: {{ auth()->guard('web')->user()->id }},
        $profileType: 'user'
    });
})();

</script>
@endsection