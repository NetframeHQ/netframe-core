@foreach($profileCommunity as $member)
    @include('join.member-card', ['profile' => $profile])
@endforeach