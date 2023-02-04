@if(
  null !== $friends &&
  $friends->blacklist == 1
)
  @if(
    $friends->users_id != auth()->guard('web')->user()->id
  )
    <a href="#"
      class="nf-btn friends-refuse disabled fn-add-friend nf-addfriend "
      data-tl-add='{{ $dataJsonEncoded }}'
    >
      <span class="btn-txt">
        {{ trans('page.blacklist') }}
      </span>
    </a>
  @else
    <a href="#"
      class="nf-btn fn-add-friend nf-addfriend "
      data-tl-unlocked='{{ $dataJsonEncoded }}'
    >
        <span class="btn-txt">
          {{ trans('page.unlocked') }}
        </span>
    </a>

  @endif
@else
  @if(
    null !== $friends &&
    $friends->blacklist == 0 &&
    $friends->status == 0 &&
    (
      $friends->users_id == auth()->guard('web')->user()->id ||
      $friends->friends_id == auth()->guard('web')->user()->id
    )
  )
    <a href="#"
      class="nf-btn btn-nohov fn-add-friend nf-addfriend"
      data-tl-add='{{ $dataJsonEncoded }}'
    >
      <span class="btn-img svgicon icon-plus d-none">
        @include('macros.svg-icons.plus')
      </span>
      <span class="btn-img svgicon icon-check">
        @include('macros.svg-icons.check')
      </span>
      <span class="btn-txt">
        {{ trans('page.add_in_progress') }}
      </span>
     </a>
  @elseif($friends)
    <a href="#" class="nf-btn nf-addfriend nf-addedfriend">
      <!-- <span class="default">&nbsp;</span> -->
      <span class="btn-img svgicon">
        @include('macros.svg-icons.check')
      </span>
      <span class="btn-txt">
        {{ trans('friends.friend_solo') }}
      </span>
    </a>

  @else
    <a href="#"
      class="nf-btn fn-add-friend nf-addfriend"
      data-tl-add='{{ $dataJsonEncoded }}'
    >
      <span class="btn-img svgicon icon-plus @if(!empty($addThis)) d-none @endif">
        @include('macros.svg-icons.plus')
      </span>
      <span class="btn-img svgicon icon-check @if(empty($addThis)) d-none @endif">
        @include('macros.svg-icons.check')
      </span>
      <span class="btn-txt">{{ !empty($addThis) ? trans('page.add_in_progress') : trans('page.add_friend') }}</span>
    </a>
  @endif
@endif