@if ($Taction->author_type == "App\User")
    <a href="{{ $Taction->author->getUrl() }}">
        {{ $Taction->author->firstname }} {{ $Taction->author->name }}
    </a>
@elseif ($Taction->author_type == "App\House")
    <a href="{{ $Taction->author->getUrl() }}">
        {{ $Taction->author->getNameDisplay() }}
    </a>
@elseif ($Taction->author_type == "App\Community")
    <a href="{{ $Taction->author->getUrl() }}">
        {{ $Taction->author->getNameDisplay() }}
    </a>
@elseif ($Taction->author_type == "App\Channel")
    <a href="{{ $Taction->author->getUrl() }}">
        {{ $Taction->author->getNameDisplay() }}
    </a>
@elseif ($Taction->author_type == "App\Project")
    <a href="{{ $Taction->author->getUrl() }}">
        {{ $Taction->author->getNameDisplay() }}
    </a>
@elseif ($Taction->author_type == 'Media')
    <a href="{{ $Taction->author->author()->first()->getUrl() }}">
        {{ $Taction->author->author()->first()->getNameDisplay() }}
    </a>
@endif

