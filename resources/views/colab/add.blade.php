<div class="modal-header">
    <h4 class="modal-title">
        {{ trans('colab.'.$mode) }}
    </h4>
    <a class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">{{trans('form.close') }}</span>
    </a>
</div>
<!-- End MODAL-HEADER -->

<div class="modal-body nf-form">
    {{ Form::open() }}
        @if(isset($id))
            <input name="id" value="{{$id}}" type="hidden">
        @endif
        <!-- NAME -->
        <label class="nf-form-cell nf-cell-full">
            {{ Form::text('doc_name', $name ?? '', ['class'=>'nf-form-input']) }}
            <span class="nf-form-label">
                {{ trans('colab.doc.name') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>

        <!-- USERS -->
        <label class="nf-form-cell nf-cell-full tags-add">
            <select name="doc_users[]" multiple="multiple" class="nf-form-input select-user">
                @if(isset($users))
                    @foreach($users as $user)
                        <option selected value="{{$user['id']}}">
                            {{$user['text']}}
                        </option>
                    @endforeach
                @endif
            </select>
            <span class="nf-form-label">
                {{ trans('colab.doc.users') }}
            </span>
            <div class="nf-form-cell-fx"></div>
        </label>

        <div class="nf-form-validation">
            <button type="submit" class="nf-btn btn-primary btn-xxl">
                <div class="btn-txt">
                    {{ trans('form.save') }}
                </div>
                <div class="svgicon btn-img">
                    @include('macros.svg-icons.arrow-right')
                </div>
            </button>
        </div>
    {{ Form::close() }}
</div>
<!-- End MODAL-BODY -->