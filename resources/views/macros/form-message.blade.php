
@if(session()->has('messageForm'))
<div class="alert alert-{{ $alertType }} fade in alert-dismissible messageForm" role="alert">

	<button type="button" class="close" data-dismiss="alert">
		<span aria-hidden="true">&times;</span>
	</button>
	{{ $content }}
</div>
@endif
