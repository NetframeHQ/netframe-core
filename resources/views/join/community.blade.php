@extends($profile->getType().'.form.main')

@section('subcontent')
    <div class="nf-form">
        <div class="nf-settings-title">
            {{ trans('members.community.'.$communityType) }}
        </div>
        <ul class="nf-list-settings">
            @if($profileCommunity->count() == 0)
                <li class="nf-list-placeholder">{{ trans('members.noUsers.'.$communityType) }}</li>
            @else
                @include('join.users-list')
            @endif

        </ul>
    </div>
@stop
@section('project.tab.javascripts')
<script>
    $(document).on('click', '.fn-resend-invitation', function(e){
        e.preventDefault();
        var el = $(this);
        var dataId = el.data('user');
        var jqXhr = $.post("{{route('join.answer',['action'=>"resend"])}}" , {
               postData : dataId
        });
    });
</script>
@endsection