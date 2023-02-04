@extends($profile->getType().'.form.main')

@section('subcontent')
    <div class="nf-form">
        <div class="nf-settings-title">
            {{ trans('members.invite') }}
            <ul class="nf-actions">
                <li class="nf-action">
                    {{ Form::open(['route' => ['join.search.users'], 'id' => 'inviteUsers']) }}
                        {{ Form::hidden('profile_id', $profile->id) }}
                        {{ Form::hidden('profile_type', class_basename($profile)) }}

                        <div class="nf-input">
                            {{ Form::text('query', '', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder'=> 'Rechercher…']) }}
                            <span class="input-group-btn">
                                <button class="nf-btn btn-ico" type="submit">
                                    <span class="svgicon btn-img">
                                        @include('macros.svg-icons.search')
                                    </span>
                                </button>
                            </span>
                        </div>
                    {{ Form::close() }}
                </li>
            </ul>
        </div>
        <div id="search-results">
            @include('join.search-results')
        </div>
    </div>
@stop

@section('javascripts')
@parent
<script>
(function($) {
    $(document).on('click', "#checkAll", function(){
        $('#inviteSelectedUsers input:checkbox').not(this).prop('checked', this.checked);
    });

    $(document).on('submit', '#inviteUsers', function(e){
        e.preventDefault();
        var _form = $(this);
        var actionUrl = _form.attr('action');
        var formData = _form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function( data ) {
                if(typeof data.viewContent != 'undefined') {
                    $('#search-results').html(data.viewContent);
                }
            },
            error: function(textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    });
})(jQuery);

</script>
@stop