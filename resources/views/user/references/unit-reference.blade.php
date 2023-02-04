@if( $reference->status == 1 || ( $rights && $rights < 3 ) )
    <li class="" id="userReference-{{ $reference->id }}">
            <a href="{{ URL::Route('tags.page', ['tagId' => $reference->reference->id, 'tagName' => str_slug($reference->reference->name)]) }}">
                #{{ $reference->reference->name }}</div>
            </a>
            {{--
            @if($reference->status == 1)
                <div class="col-xs-6 col-md-6">
                    @if( $rights && $rights < 3 )
                        <a href="{{ url()->route('user.reference.delete', ['id' => $reference->id ] ) }}" title="{{ trans('netframe.delete') }}"
                            class="fn-confirm-delete link-netframe float-right" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                        </a>
                    @endif

                    {!! HTML::likeBtn(['liked_id' => $reference->id,
                          'liked_type' => get_class($reference),
                          'liker_id' => auth()->guard('web')->user()->id,
                          'liker_type' => 'user',
                          'idNewsFeeds' => null
                          ],
                          false,
                          $reference->like,
                          'btn-xs float-right')!!}
                </div>
            @endif
            --}}
        </li>
@endif