<div class="content-sidebar-block">
    <div class="content-sidebar-line">
        <span class="svgicon icon-tags">
            @include('macros.svg-icons.tags')
        </span>
        <h4 class="content-sidebar-title" title="{{ trans('user.references') }}">
            @if($profile->id == auth('web')->user()->id)
                {{ trans('user.references') }}
            @else
                {{ trans('user.hisReferences') }}
            @endif
        </h4>
        <ul class="list-unstyled tags-list" id="userReferenceList">
        @if( $profile->userReferences->count() > 0)
            @foreach($profile->userReferences as $reference)
                @if($reference->status == 1 || ( $rights && $rights < 3 ) )
            <li id="userReference-{{ $reference->id }}">
                <a href="{{ url()->route('tags.page', ['tagId' => $reference->reference->id, 'tagName' => str_slug($reference->reference->name)]) }}">
                    #{{ $reference->reference->name }}
                </a>
                @if($reference->status == 1)
                        @if($rights && $rights < 3)
                            <a href="{{ url()->route('user.reference.delete', ['id' => $reference->id ] ) }}" title="{{ trans('netframe.delete') }}"
                                class="fn-confirm-delete link-netframe action" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                                <span class="svgicon">
                                    @include('macros.svg-icons.trash')
                                </span>
                            </a>
                        @endif

                        {{--
                            {!! HTML::likeBtn(['liked_id' => $reference->id,
                                            'liked_type' => get_class($reference),
                                            'liker_id' => auth()->guard('web')->user()->id,
                                            'liker_type' => 'user',
                                            'idNewsFeeds' => null
                                            ],
                                            (in_array($reference->id, $userLikedReferences)) ? true : false,
                                            $reference->like,
                                            'btn-xs action')!!}
                        --}}
                @elseif($rights && $rights < 3)
                        <a id="deleteReference" href="{{ url()->route('user.reference.delete', ['id'=>$reference->id ] ) }}" class="fn-confirm-delete link-netframe action" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                            <span class="svgicon">
                                @include('macros.svg-icons.trash')
                            </span>
                        </a>

                        <a id="validReference" href="{{ url()->route('user.reference.valid', ['id'=>$reference->id ] ) }}" class="fn-valid-user-ref btn-netframe btn-xs action" data-reference-id="{{ $reference->id }}">
                            <span class="svgicon tagaction">
                                @include('macros.svg-icons.check')
                            </span>
                        </a>
                @endif
            </li>
                @endif
            @endforeach
        @elseif($rights && $rights < 3)
            <p class="tl-wording">
                {{ trans('user.giveReferences') }}
            </p>
        @endif
        </ul>
    </div>
    {{-- limit to 15 max ref and 3 max for other user if not validated --}}
    @if($profile->userReferences->count() < 15 && ( ( $rights && $rights < 3 ) || (!$rights && auth()->guard('web')->user()->postedUserReferences($profile->id, 0)->count() < 3) ) )
        {{ Form::open( ['id' => 'addReferenceForm', 'class' => 'no-auto-submit']) }}
            <div id="tl-form-user-references" class="tags-add">
                {{ Form::select('name', [], '', ['class' => 'form-control form-control-sm fn-select2-tag '.(($errors->has('name')) ? 'is-invalid' : ''), 'style' => 'min-width:100%']) }}
                @if ($errors->has('name'))
                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                @endif

                <button type="submit" id="addReferenceFormButton" class="nf-btn">
                    <span class="btn-txt">
                        {{ trans('user.addRef') }}
                    </span>
                </button>
            </div>
        {{ Form::close() }}
    @endif
</div>


@section('javascripts')
    @parent
<script>
(function($){
    $(document).on('click', '.fn-valid-user-ref', function(event){
        event.preventDefault();
        el = $(this);
        referenceId = el.data('reference-id');
        url = $(this).attr('href');

        //send reference id in validator controller return get unit view
        $.post(url, {referenceId: referenceId }).success(function (data) {
            elTarget = $(data.targetId);
            //modify reference display
            elTarget.fadeOut('slow', function() {
                elTarget.replaceWith(data.viewReplace);
                elTarget.fadeIn('slow');
            });
        });
    });
})(jQuery);

// javascript form
(function($){

    $(document).on('submit', '#addReferenceForm', function(event){
        event.preventDefault();
        _form = $(this).closest('form');
        inputReference = _form.find('select[name=name]');
        newReference = inputReference.val()[0];
        console.log(newReference);

        //send new reference in ajax and get return clear form
        $.post("{{ url()->to('/') }}" + laroute.route('user.reference.add'), {
            'userId': {{ $profile->id }},
            'newReference': newReference
        }).success(function (data) {
            elTarget = '#userReferenceList';
            inputReference.val('');
            $('.fn-select2-tag').trigger('change.select2');

            //alert when already exiusts
            if(typeof data.exists != 'undefined') {
                alert("{{ trans('user.refExists') }}");
            }

            if($(elTarget).children('.tl-wording').length > 0){
                $('.tl-wording').remove();
            }

            //display new reference
            if(typeof data.view != 'undefined') {
                $(data.view).appendTo($(elTarget)).show().slideDown('normal');
                if( data.totalReferences >14 || data.postedByOther > 2){
                    $('#tl-form-user-references').remove();
                }
            }
        });
    });

    //$.fn.select2.defaults.set('amdLanguageBase', 'assets/vendor/select2/i18n/');
    //$.fn.select2.defaults.set('language', 'fr');

    $('.fn-select2-tag').select2({
        placeHolder:'tapez ici',
        minimumInputLength: 2,
        multiple: true,
        maximumSelectionLength: 1,
        maximumSelectionSize:function(){
            return 1;
        },
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: "{{ URL::route('tags.autocomplete') }}",
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
})(jQuery);
</script>
@stop