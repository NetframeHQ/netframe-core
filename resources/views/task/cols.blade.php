@foreach($cols as $key=>$value)
	<div class="form-group">
		<label>{{ucfirst($value['name'])}}</label>
        <div class="input-group">
        	@if($value['type']=='tag')
        		<select name="cols[{{$key}}][]" multiple class="select-tag form-control">
                    @if(isset($pcols[$key]))
                        @foreach($pcols[$key] as $tag)
                            <option selected="selected" value="{{$tag->id}}">{{$tag->name}}</option>
                        @endforeach               
                    @endif
                </select>
        	@elseif($value['type']=='user')
        		<select name="cols[{{$key}}][]" multiple class="select-user form-control">
                    @if(isset($pcols[$key]))
                        @foreach($pcols[$key] as $user)
                            <option selected="selected" value="{{$user->id}}">{{$user->getNameDisplay()}}</option>
                        @endforeach               
                    @endif
                </select>
            @elseif($value['type']=='file')
                {{ Form::hidden('mediasIds', (isset($mediasIds) ? $mediasIds : ''), ['id' => 'postSelectedMediasId']) }}
        	@else
            <input name="cols[{{$key}}]" type="{{$value['type']}}" class="form-control">
            @endif
        </div>
    </div>
@endforeach