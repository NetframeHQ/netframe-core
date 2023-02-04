<?php $cptMosaic = 0; ?>
<ul class="block-mosaic mosaic-container feed-mosaic">
    @foreach($profiles as $profile)
    <li class="mosaic-item h-card col-xs-3 col-lg-3 col-md-3 col-sm-3">
        <span class="mosaic-icon icon-{{ $profile->getType() }}">
            <span class="icon ticon-{{ $profile->getType() }}"></span>
        </span>

        <div class="mosaic-content">
            <a href="{{ url()->route('identity.card', array('profil' => $profile->getType(), 'id' => $profile->id, 'prevId' => $profile->prevId, 'nextId' => $profile->nextId, 'prevProfile' => $profile->prevProfile, 'nextProfile' => $profile->nextProfile )) }}"
                class="mosaic-picture" data-toggle="modal" data-target="#modal-ajax">
                {!! HTML::thumbnail($profile->mosaicImage(), '', '', array('class' => 'u-photo img-fluid'), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
            </a>
        </div>
        <div class="mosaic-footer">
            {{--
            @if($profile->isBuzz())
                <span class="icon ticon-recognition float-left"></span>
            @endif
            --}}
            <h2 class="mosaic-name p-name">
                <a href="{{ url()->route('identity.card', array('profil' => $profile->getType(), 'id' => $profile->id, 'prevId' => $profile->prevId, 'nextId' => $profile->nextId, 'prevProfile' => $profile->prevProfile, 'nextProfile' => $profile->nextProfile )) }}"
                    class="u-url profile-container" data-toggle="modal" data-target="#modal-ajax"
                    data-profile-id="{{ $profile->id }}"
                    data-profile-type="{{ $profile->getType() }}" id="{{ $profile->getType() }}_{{ $profile->id }}">
                    {!! HTML::online($profile, true) !!} {{ ucfirst($profile->getNameDisplay()) }}
                </a>
            </h2>

            @if($profile->getType() != 'user')
                <a title="{{ trans('netframe.bookmarkAlt') }}" class="bookmarkProfile fn-tl-clip" data-profile-id="{{ $profile->id }}"
                    data-profile-type="{{ $profile->getType() }}"
                    @if(isset($instantPlaylistProfiles[get_class($profile)][$profile->id]) )
                        disabled
                    @endif
                >
                <span class="icon ticon-clip @if($profile->isInstantBookmarkedByCurrentUser())
                        text-secondary
                    @endif"></span>
                </a>
            @endif
        </div>
    </li>
    <?php $cptMosaic++; ?>
    @endforeach
</ul>