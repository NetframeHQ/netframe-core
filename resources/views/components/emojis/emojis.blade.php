<li class="nf-action emoji-keyboard {{ $additionalClass }}">
    <a class="nf-btn btn-ico btn-nobg fn-display-emojis-panel">
        <span class="svgicon">
            @include('macros.svg-icons.emoji')
        </span>
    </a>
    <div class="emojis-panel">
        <div id="emojis-{{ $currentGroup }}">
            <ul class="nav nav-tabs">
                @foreach($emojis as $grId=>$emojisGroup)
                    <li role="presentation" class="@if($emojisGroup['order'] == 1) active @endif">
                        <a href="#emojis-group-{{ $grId }}" role="tab" data-toggle="tab">
                            {{ $emojisGroup['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @foreach($emojis as $grId=>$emojisGroup)
                <div class="emojis-group @if($emojisGroup['order'] == 1) active @endif" id="emojis-group-{{ $grId }}">
                    <ul class="list-inline" data-target="{{ $fieldTarget }}">

                        @foreach($emojisGroup['emojis'] as $emId=>$emoji)
                            <li class="list-inline-item">
                                <a class="fn-add-unicode" data-id="{{$emId}}" data-unicode="{{ $emoji }}">{{ $emoji }}</a>
                            </li>
                        @endforeach

                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</li>

@if(isset($fromAjax) && !$fromAjax)
    @section('javascripts')
	@parent
        <script>
        (function($){
            if(!mobile){
                $("#emojis-{{ $currentGroup }}").tabs();
                // get all emojis by group
                {{--
                @foreach($emojisGroups as $emojisGroup)
                    var data = {
                            groupId: {{ $emojisGroup->id }}
                    };
                    $.ajax({
                        url: laroute.route('emojis.emojis'),
                        type: "POST",
                        data: data,
                        success: function(data) {
                            var source   = $("#emoji-group-render").html();
                            var template = Handlebars.compile(source);
                            var context = data;
                            var html = template(context);
                            $("#emojis-group-"+{{ $emojisGroup->id }}+" ul").append(html);
                        },
                        error: function() {
                        }
                    });
                @endforeach
                --}}
            }

        })(jQuery);
        </script>
    @stop
@else
    <script>
    (function($){
        if(!mobile){
            $("#emojis-{{ $currentGroup }}").tabs();
            // get all emojis by group
            {{--
            @foreach($emojisGroups as $emojisGroup)
                var data = {
                        groupId: {{ $emojisGroup->id }}
                };
                $.ajax({
                    url: laroute.route('emojis.emojis'),
                    type: "POST",
                    data: data,
                    success: function(data) {
                        var source   = $("#emoji-group-render").html();
                        var template = Handlebars.compile(source);
                        var context = data;
                        var html = template(context);
                        $("#emojis-group-"+{{ $emojisGroup->id }}+" ul").append(html);
                    },
                    error: function() {
                    }
                });
            @endforeach
            --}}
        }

    })(jQuery);
    </script>
@endif


{{-- @include('components.emojis.emoji') --}}