    @if($offer->author->getType() == 'user')
        <a href="{{ url()->to('messages/form-message', ['user', $offer->users_id, 'user', auth()->guard('web')->user()->id, 10]) }}/?offerId={{$offer->id}}" class="btn btn-default float-right" data-toggle="modal" data-target="#modal-ajax">
    @elseif($offer->author->getType() != 'project')
        <a href="{{ url()->to('messages/form-message', [$offer->author->getType(), $offer->author->id, 'user', auth()->guard('web')->user()->id, 10]) }}/?offerId={{$offer->id}}" class="btn btn-default float-right" data-toggle="modal" data-target="#modal-ajax">
    @endif

    @if($offer->offer_type == 'demand')
            {{ trans('offer.contactDemand') }}
        @else
            {{ trans('offer.contactOffer') }}
        @endif
    </a>
