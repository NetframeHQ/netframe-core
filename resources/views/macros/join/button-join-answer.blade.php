@if($simpleButton)
    <li class="nf-action">
        <a href="#" class="nf-btn fn-join-answer" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'contributor'>
            <span class="btn-txt">{{ trans('members.accept.accept') }}</span>
        </a>
    </li>
    <li class="nf-action">
        <a href="#" class="nf-btn fn-join-answer" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'refuse'>
            <span class="btn-txt">{{ trans('members.accept.deny') }}</span>
        </a>
    </li>
@else
    <li class="nf-action">
        <a href="#" class="nf-btn fn-join-answer" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'participant'>
            <span class="btn-txt">{{ trans('members.accept.asParticipant') }}</span>
        </a>
    </li>
    <li class="nf-action">
        <a href="#" class="nf-btn fn-join-answer" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'contributor'>
            <span class="btn-txt">{{ trans('members.accept.asContributor') }}</span>
        </a>
    </li>
    <li class="nf-action">
        <a href="#" class="nf-btn fn-join-answer" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'refuse'>
            <span class="btn-txt">{{ trans('members.accept.deny') }}</span>
        </a>
    </li>
@endif