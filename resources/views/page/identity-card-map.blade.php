<div class="identity-map-card">
    <div class="clearfix">
            <span class="mg-right-5 float-left">
                {!! HTML::thumbImage($profile->profile_media_id, 60, 60, [], $profile->getType()) !!}
            </span>
            <h4>
                <a href="{{ $profile->getUrl() }}">
                    {{ $profile->getNameDisplay() }} {!! HTML::online($profile, true) !!}
                </a>

            </h4>
            @if( $profile->getType() == "user" && $profile->phone != '')
                <p>
                    {{ trans('user.phone') }} : {{ \App\Helpers\StringHelper::formatPhoneNumber($profile->phone) }}
                </p>
            @endif

            @if(in_array(class_basename($profile), config('netframe.model_taggables')))
                @include('tags.element-display', ['tags' => $profile->tags])
            @endif


    </div>
    <div class="clearfix">
        <a class="btn btn-sm btn-arrow-card float-left" href="{{ $profile->getUrl() }}">
            {{ trans('netframe.viewProfile') }}
        </a>
        <a id="nextProfile" class="btn btn-sm skip-map btn-arrow-card float-right" data-toggle="tooltip" title="{{ trans('netframe.next') }}">
            {{ trans('netframe.next') }}
        </a>
    </div>
</div>
