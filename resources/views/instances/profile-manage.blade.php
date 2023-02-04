<li class="nf-list-setting member-{{ $profile->id }}">
    <a class="nf-invisiblink" href="{{ $profile->getUrl() }}"></a>
    @if($mainProfile->getType()!="channel" && $mainProfile->mosaicImage() != null)
        <span class="avatar">
            {!! HTML::thumbnail(
                $mainProfile->mosaicImage(),
                '30',
                '30',
                array('class' => 'float-left'),
                asset('assets/img/avatar/'.$member->getType().'.jpg'),
                null,
                $mainProfile->getType()
            ) !!}
        </span>
    @else
        <span class="svgicon">
            @include('macros.svg-icons.'.$profile->getType())
        </span>
    @endif
    <div class="nf-list-infos">
        <div class="nf-list-title">
            {{ $mainProfile->getNameDisplay() }}
        </div>
        <div class="nf-list-subtitle">
            {{ trans('instances.profiles.createdAt') }} : {{ \App\Helpers\DateHelper::feedDate($mainProfile->created_at) }}
        </div>
    </div>
    <ul class="nf-actions">
        {!! HTML::userRights($mainProfile, $relatedProfile, '.member-' . $profile->id) !!}
        @if($profile->active == 0)
            <li class="nf-action">
                <div class="nf-lbl">
                    <span class="lbl-txt">
                        {{ trans('instances.profiles.disabled') }}
                    </span>
                </div>
            </li>
        @endif
    </ul>
</li>
