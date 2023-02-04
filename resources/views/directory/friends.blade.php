@foreach($friends as $friend)
  <li>
    <a href="{{ \App\Helpers\StringHelper::uriHomeUserObject($friend) }}" class="nf-user">
      @if($friend->profileImage != null)
        <div class="nf-avatar">
          {!! HTML::thumbnail($friend->profileImage, '40', '40', array('class' => 'img-fluid'), asset('assets/img/avatar/user.jpg')) !!}
      @else
        <div class="nf-avatar svgicon">
          @include('macros.svg-icons.user')
      @endif
      </div>

      <p class="tx-content">
          {{ $friend->firstname }} {{ $friend->name }}
      </p>

      <div class="nf-actions">
          {!! HTML::deleteFriendBtn(['friend_id' => $friend->id,'users_id'  => auth()->guard('web')->user()->id]) !!}
      </div>
    </a>
  </li>
@endforeach
