<li class="template-download fade mosaic-item col-md-2 col-xs-3 file-{{ $id }}" data-file-id="{{ $id }}">
            @if(!isset($fromXplorer))
                <a class="fn-remove-media">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            @endif
            @if($mediaPlatform == "local")
                @if($type == "0")
                    <img src="/media/download/{{ $id }}?thumb=1" class="img-fluid" />
                @endif

                @if($type == "1")
                    <div class="square-box"><div class='square-content'><div><span>
                        <p><span class="glyphicon glyphicon-facetime-video"></span></p>
                        <p>{{ $name }}</p>
                    </span></div></div>
                @endif

                @if($type == "2")
                    <div class="square-box"><div class='square-content'><div><span>
                    <p><span class="glyphicon glyphicon-headphones"></span></p>
                        <p>{{ $name }}</p>
                    </span></div></div>
                @endif

                @if($type == "3")
                    <div class="square-box"><div class='square-content'><div><span>
                        <p><span class="glyphicon glyphicon-file"></span></p>
                        <p>{{ $name }}</p>
                    </span></div></div>
                @endif

                @if($type == "4")
                    <div class="square-box"><div class='square-content'><div><span>
                    <p><span class="glyphicon glyphicon-folder-close"></span></p>
                        <p>{{ $name }}</p>
                    </span></div></div>
                @endif
            @endif

            @if($mediaPlatform == "vimeo")
                <img src="/media/download/{{ $id }}?thumb=1" class="img-fluid" />
            @endif

            @if($mediaPlatform == "youtube")
                <img src="/media/download/{{ $id }}?thumb=1" class="img-fluid" />
            @endif

            @if($mediaPlatform == "dailymotion")
                <img src="/media/download/{{ $id }}?thumb=1" class="img-fluid" />
            @endif

            @if($mediaPlatform == "soundcloud")
                <img src="/media/download/{{ $id }}?thumb=1" class="img-fluid" />
            @endif
    </li>
    @if(isset($fromXplorer))
        <li class="col-md-10 col-xs-9 file-{{ $id }}" data-file-id="{{ $id }}">
            <a class="fn-remove-media float-right">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
            <input type="text" name"filename-{{ $id }}" value="{{ $name }}">
        </li>
    @endif