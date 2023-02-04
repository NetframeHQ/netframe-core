@if($profile->confidentiality == 0 && !App\Http\Controllers\BaseController::hasViewProfile($profile))

@else
    <div class="content-sidebar-action">
        <a class="button @if($followed) status-subscribed show-leave @endif fn-like-profile button-subscribe @if($nbFollowers > 0) counter @endif"  data-tl-subscrib='{{ $dataJsonEncoded }}'>
            <span class="default">{{ trans('netframe.subscribe') }}</span>
            <span class="subscribed">
                <span class="svgicon icon-check">
                    @include('macros.svg-icons.check')
                </span>
                {{ trans('netframe.subscribed') }}
            </span>
            <span class="leave">
                <span class="svgicon icon-check">
                    @include('macros.svg-icons.leave')
                </span>
                {{ trans('netframe.unsubscribe') }}
            </span>
            <span class="num @if($nbFollowers == 0) d-none @endif"  data-toggle="modal" data-target="modal-ajax-thin">{{ $nbFollowers }}</span>
        </a>
    </div>
@endif