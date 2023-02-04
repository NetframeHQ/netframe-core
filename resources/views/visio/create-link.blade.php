<a class="nf-btn btn-xl" href="{{ url()->route('visio.manage.link', ['channelId' => $channel->id]) }}" data-target="#modal-ajax" data-toggle="modal">
  <span class="svgicon btn-img">
      @include('macros.svg-icons.visio')
  </span>
  <span class="btn-txt">
    {{ trans('channels.visio.createLink') }}
  </span>
</a>