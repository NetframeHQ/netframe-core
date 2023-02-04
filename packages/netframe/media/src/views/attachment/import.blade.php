<div class="supportedPlatforms">
    <span>{{ trans('media::messages.import_supported_platforms') }}:</span>

    @foreach($importers as $importer)
        {{ $importer->getDescription()['name'] }}
        <span class="{{ $importer->getDescription()['icon'] }}"></span>
    @endforeach
</div>

{{ Form::open(array('route' => 'media_import', 'id' => 'import', 'name' => 'import', 'class' => 'importForm')) }}

	<div id="importUrl" class="form-group">

	    <div class="input-group">
            {{ Form::text('url', null, array('class' => 'form-control', 'placeholder' => trans('media::messages.enter_url'), 'required' => true)) }}
            <span class="input-group-btn">
	            <button type="submit" class="btn btn-default">{{ trans('media::messages.import') }}</button>
	        </span>
	    </div>

	    <span class="help-block"></span>
	</div>

{{ Form::close() }}
