@extends('layouts.page')

@section('title')
    {{ trans('netframe.leftMenu.notes') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    <style>
        .search{position: relative; margin: 15px 10px}
        .search-submit{position: absolute; right: 2px; top: 0; background: transparent}
        .right{float: right;}
        .primary{cursor: pointer;}
        .doc{min-height: 45px; border-left: 30px solid transparent; border-bottom: 1px solid #ccc5; cursor: pointer; padding: 7px 15px; border-radius: 5px; transition-duration: .2s}
        .doc.row{border-left: 0}
        .doc:hover{background: #ccc3}
        .doc:hover ul{display: block;}
        .doc .name{float: left; line-height: 30px}
        .doc .name{font-weight: 600; float: left;}
        .main-container{max-width: 100%!important}
        .fullscreen{left: 0!important;}
        .top{top: 0!important; z-index: 100; background: #fff}
        .hide-wrapper{width: 0}
    </style>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
@endsection

@section('content')
<div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-notes">
            @include('macros.svg-icons.notes_big')
        </span>
        <h2 class="main-header-title">
            <a href="/collab">{{ trans('netframe.leftMenu.notes') }}</a>
        </h2>
    </div>
    <ul class="nf-actions">
        <li class="nf-action">
            <a class="nf-btn" data-target="#modal-ajax" data-toggle="modal" href="/api/{{auth()->guard('web')->user()->slug}}/add">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.plus')
                </span>
                <span class="btn-txt">
                    {{ trans('colab.create') }}
                </span>
            </a>
        </li>

        {{-- CUSTOM NAV GRID/LIST --}}
        {{--
        <li class="nf-action nf-custom-nav">
            <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img {{ (session('userXplorerView') == 'grid') ? 'd-none' : '' }}">
                    @include('macros.svg-icons.list-line')
                </span>
                <span class="svgicon btn-img {{ (session('userXplorerView') == 'list') ? 'd-none' : '' }}">
                    @include('macros.svg-icons.list-grid')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    <li>
                        <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'list') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-list-wrapper" data-view-slug="list">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.list-line')
                            </span>
                            <span class="btn-txt">
                                Visualiser en ligne
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'grid') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-grid-wrapper" data-view-slug="grid">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.list-grid')
                            </span>
                            <span class="btn-txt">
                                Visualiser en grille
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        --}}


        <li class="nf-action">
            <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.filters')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    <li>
                        <a class="nf-btn btn-order" href="#" data-by="name">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.filters')
                            </span>
                            <span class="btn-txt">
                                {{ trans('colab.doc.name') }}
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="nf-btn btn-order" href="#" data-by="edit">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.filters')
                            </span>
                            <span class="btn-txt">
                                {{ trans('colab.doc.last_edit') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>
<div class="main-container">
    <div id="nav_skipped" class="main-scroller">
        <div id="collab"></div>
    </div>
</div>
@endsection

@php
	$profile = auth()->guard('web')->user();
@endphp
{{-- @section('sidebar')
    @include('components.sidebar-user')
@stop --}}
<!-- colab -->
@section('javascripts')
@parent
<script>
    localStorage.setItem("apiToken", "{{auth()->guard('web')->user()->slug}}");
    localStorage.setItem("userId", "{{auth()->guard('web')->user()->id}}");
    localStorage.setItem("name", "{{auth()->guard('web')->user()->getNameDisplay()}}");
    localStorage.setItem("image", "{{auth()->guard('web')->user()->profileImageThumbUrl()}}");
    localStorage.setItem("initials", "{{auth()->guard('web')->user()->initials()}}");
    localStorage.setItem("initialsToColor", "{{auth()->guard('web')->user()->initialsToColor()}}");

    $(document).ready(function(){
        $(document).on('click', '.btn-order', function(e){
          window.history.pushState(null, null, "?order_by="+$(this).data('by'));
          $('#order-input').val($(this).data('by')).dispatchEvent(new Event('input'));
          return false;
        })
        $('.content-sidebar.ps').remove()

        $(document).on('click', '.hide-sidebar', function(e){
            $('.sidebar-wrapper').toggleClass("hide-wrapper")
            $('.wrapper .content').toggleClass("fullscreen")
            $('.main-container').toggleClass("top")
        })

        $('#modal-ajax').on('shown.bs.modal', function(){
            sel2()
        });
        $( document ).ajaxSuccess(function( event, xhr, settings ) {
          if ( xhr.responseJSON.reload ) {
            window.location.reload(false);
          }
        });
        $(document).on('click', '.delete-el', function(e) {
            var _confirm = confirm('{{ trans('task.confirmDelete') }}');

            if (!_confirm) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            else{
                e.preventDefault();
                var el = $(this);
                var panel = el.closest("li.task");

                var dataId = {id: el.data('id') };

                var jqXhr = $.post("{{url()->route('colab.delete', ['slug'=>auth()->guard('web')->user()->slug])}}" , {
                    postData : dataId
                });

                jqXhr.success(function(data) {
                    if(dataId){
                        panel.fadeOut();
                    }
                });
            }
            return false;
        });
        $(".select-user > option").prop("selected","selected"); $(".select-user").trigger("change");
    });
function sel2(){
    $('.select-user').select2({
        dropdownParent: $("#modal-ajax"),
        placeholder: "{{ trans('form.inputUser') }}",
        // minimumInputLength: 1,
        multiple: true,
        templateResult: format,
        templateSelection: format,
        ajax: {
            url: "{{url()->route('colab.user')}}",
            dataType: "json",
            type: "POST",
            data: function (params) {
                return {
                    query: params.term
                };
            },
            processResults: function (data, page) {
                return data;
            },
        },
        escapeMarkup: function(m) { return m; },
    });
}
function format(state) {
    // alert(JSON.stringify(state))
    if (!state.image) return "<span class=\"user-avatar-initials size-25 avatar float-left\" style=\"background-color:rgb(" + state.initialsToColor + ")\"><span class=\"initials-letters\">" + state.initials + "</span></span> " +state.text;
    //var originalOption = state.element;
    return "<img class='flag' src='" + state.image + "' width='25' height='25' style='margin-right: 10px; background: #fff; border-radius:100%' />" + state.text;
}
</script>
{{ HTML::script('js/collab.js') }}
@endsection
