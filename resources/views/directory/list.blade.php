@foreach($results as $profile)
  <li class="nf-user">
    <a href="{{$profile->getUrl()}}" class="nf-invisiblink" title="{{ucfirst($profile->getNameDisplay())}}" data-id="{{$profile->id}}"></a>
      @if($profile->profileImage != null)
        <div class="nf-avatar">
          {!! HTML::thumbnail($profile->profileImage, 60, 60, [], asset('assets/img/avatar/user.jpg')) !!}
        </div>
      @else
        {{--
        <div class="nf-avatar svgicon">
          @include('macros.svg-icons.user')
        </div>
        --}}
        {!! HTML::userAvatar($profile, 30, 'nf-avatar') !!}
      @endif

      <p class="tx-content">
        {{ucfirst($profile->getNameDisplay())}}
      </p>

      @if($profile->id != auth()->guard('web')->user()->id)
        <div class="nf-actions">
          {!! HTML::addFriendBtn(['author_id' => $profile->id,'user_from' => auth()->guard('web')->user()->id,'author_type'  => 'user'], \App\Friends::relation($profile->id) ) !!}
        </div>
      @endif
  </li>
@endforeach