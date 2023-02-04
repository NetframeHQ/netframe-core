<a href="#" class="btn btn-block btn-info btn-xs fn-join-invite" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'accept'>
    {{ trans('members.accept.invite') }}
</a>
<a href="#" class="btn btn-block btn-danger btn-xs fn-join-invite" data-tl-user='{{ $dataJsonEncoded }}' data-tl-action = 'deny'>
    {{ trans('members.accept.deny') }}
</a>