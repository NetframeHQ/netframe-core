<a href="{{ $profile->getUrl() }}" class="panel-document">
    <div class="panel-document-head">
        <div class="panel-document-icon">
            @if($profile->profileImage != null)
                {!! HTML::thumbnail($profile->profileImage, '40', '40', array('class' => 'img-thumbnail img-fluid profile-image float-left'),asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
            @else
                <span class="svgicon">
                    @include('macros.svg-icons.'.$profile->getType().'_big')
                </span>
            @endif
        </div>
        <div class="panel-document-info">
            <h3 class="panel-document-title">
                {{ $profile->getNameDisplay() }}
            </h3>
            <p class="panel-document-subtitle">
                {{ $profile->users()->count() }} {{ trans_choice('page.members', $profile->users()->count()) }}
            </p>
        </div>
    </div>
</a>