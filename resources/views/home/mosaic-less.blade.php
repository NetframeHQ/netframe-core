@foreach($profiles as $profile)
<li class="mosaic-item h-card col-xs-4 col-lg-2 col-md-2 col-sm-2">
    <span class="mosaic-icon icon-{{ $profile->getType() }}">
        <span class="icon ticon-{{ $profile->getType() }}"></span>
    </span>

    <div class="mosaic-content">
        {{--
        <a href="{{ url()->route('identity.card', array('profil' => $profile->getType(), 'id' => $profile->id, 'prevId' => $profile->prevId, 'nextId' => $profile->nextId, 'prevProfile' => $profile->prevProfile, 'nextProfile' => $profile->nextProfile )) }}"
            class="mosaic-picture" data-toggle="modal" data-target="#modal-ajax">
        --}}
        <a href="{{ $profile->getUrl() }}" class="mosaic-picture">
            <img src="/assets/img/avatar/{{ $profile->getType() }}.jpg" class="u-photo img-fluid lazy" data-original="{{ ($profile->mosaicImage() != null) ? $profile->mosaicImage()->getUrl().'?thumb=1' : ''}}">
            {{-- HTML::thumbnail($profile->mosaicImage(), '', '', array('class' => 'u-photo img-fluid', 'data-original' => $profile->mosaicImage()), asset('assets/img/avatar/'.$profile->getType().'.jpg')) --}}
        </a>
    </div>
    <div class="mosaic-footer">
        <div class="float-right relation-ok">
        @if($profile->getType() == 'user' && array_key_exists($profile->id, $userRelations['friends']) && $userRelations['friends'][$profile->id]->status == 1)
            <span class="glyphicon glyphicon-user"></span>
        @elseif($profile->getType() != 'user' && array_key_exists($profile->id, $userRelations['membership'][$profile->getType()]) && $userRelations['membership'][$profile->getType()][$profile->id] == 1)
            <span class="glyphicon glyphicon-user"></span>
        @endif

        @if(in_array($profile->id, $userRelations['subscriptions'][$profile->getType()]))
                <span class="glyphicon glyphicon-ok"></span>
        @endif
        </div>
        <h2 class="mosaic-name p-name">
            <a href="{{ $profile->getUrl() }}" class="u-url profile-container" data-profile-id="{{ $profile->id }}"
                data-profile-type="{{ $profile->getType() }}" id="{{ $profile->getType() }}_{{ $profile->id }}">
                {!! HTML::online($profile, true) !!} {{ ucfirst($profile->getNameDisplay()) }}
            </a>
        </h2>
    </div>
</li>
@endforeach