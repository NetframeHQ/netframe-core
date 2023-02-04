@if ($Taction->author_type == 'Media')
    <a href="{{ $Taction->author->author()->first()->getUrl() }}" class="panel-document">
@else
    <a href="{{ $Taction->author->getUrl() }}" class="panel-document">
@endif
    <div class="panel-document-head">
        <div class="panel-document-icon">
            @if (!in_array($Taction->author_type, ['App\Media', 'App\AngelsReference', 'App\UsersReference', 'App\Share', 'App\News', 'App\TEvent']))
                @if($Taction->author->profileImage != null)
                    {!! HTML::thumbnail($Taction->author->profileImage, '40', '40', array('class' => 'img-thumbnail img-fluid profile-image float-left'),asset('assets/img/avatar/'.$Taction->author->getType().'.jpg')) !!}
                @else
                    <span class="svgicon">
                        @include('macros.svg-icons.'.$Taction->author->getType().'_big')
                    </span>
                @endif
            @endif
        </div>
        <div class="panel-document-info">
            <h3 class="panel-document-title">
                @if ($Taction->author_type == 'Media')
                    {{ $Taction->author->author()->first()->getNameDisplay() }}
                @else
                    {{ $Taction->author->getNameDisplay() }}
                @endif
            </h3>
            <p class="panel-document-subtitle">
                @if ($Taction->author_type == 'Media')
                @else
                    @if(class_basename($Taction->author) == 'User')
                    @else
                        {{ $Taction->author->users()->count() }} {{ trans_choice('page.members', $Taction->author->users()->count()) }}
                    @endif
                @endif
            </p>
        </div>
        <!-- @if ($Taction->author_type == 'Media')
            <a href="{{ $Taction->author->author()->first()->getUrl() }}" class="nf-btn">
        @else
            <a href="{{ $Taction->author->getUrl() }}" class="nf-btn">
        @endif
            @if ($Taction->author_type == 'Media')
            @else
                <span class="btn-txt">{{ trans('netframe_actions.link'.$Taction->author->getType()) }}</span>
            @endif
        </a> -->
    </div>
</a>