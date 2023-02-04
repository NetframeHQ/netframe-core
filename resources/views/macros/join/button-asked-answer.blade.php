<ul class="nf-actions">
	<li class="nf-action">
		<button class="btn btn-success fn-ask-friend {{ $btnSize }}" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'accepted'>{{ trans('notifications.invite.accept') }}</button>
	</li>
	<li class="nf-action">
		<button class="btn btn-warning fn-ask-friend {{ $btnSize }}" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'deny'>{{ trans('notifications.invite.deny') }}</button>
	</li>
	<li class="nf-action">
		<button class="btn btn-danger fn-ask-friend {{ $btnSize }}" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'blacklist'>{{ trans('notifications.invite.blacklist') }}</button>
	</li>
</ul>	
	
	

