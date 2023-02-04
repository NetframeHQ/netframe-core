
@if($tags != null && count($tags) > 0)
    <ul class="list-unstyled tags-list">
        @foreach($tags as $tag)
            @if($tag->name != null)
                <li><a href="{{ URL::Route('tags.page', ['tagId' => $tag->id, 'tagName' => str_slug($tag->name)]) }}">#{{ $tag->name }}</a></li>
            @endif
        @endforeach
    </ul>
@endif