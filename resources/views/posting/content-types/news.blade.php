<div class="form-group">
    {{ Form::textarea('content', (isset($post['content']) ? $post['content'] : null), ['rows' => 1, 'class'=>'form-control mentions autogrow panel-textarea '.(($errors->has('content')) ? 'is-invalid' : ''), 'id' => 'form-post-content', 'placeholder' => trans('form.firstPost.example')] ) }}
    {!! $errors->first('content', '<p class="invalid-feedback">:message</p>') !!}
</div>

<div class="imported-link">
@if(isset($linksIds) && !empty($linksIds))
    @foreach(explode(',',$linksIds) AS $linkId)
        @include('posting.content-types.link-preview', ['id' => $linkId])
    @endforeach
@endif
</div>