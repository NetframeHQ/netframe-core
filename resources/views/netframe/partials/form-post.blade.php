<div class="form-group">
    {{ Form::label('content', trans('form.message')) }}
    <br />
    {{ Form::textarea('content', (isset($inputOld['content']) ? $inputOld['content'] : null), ['rows'=>'7', 'class'=>'form-control '.(($errors->has('content')) ? 'is-invalid' : ''), 'id' => 'form-post-content'] ) }}
    {{ $errors->first('content', '<p class="invalid-feedback">:message</p>') }}
</div>