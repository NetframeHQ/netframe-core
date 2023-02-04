<script id="emoji-group-render" type="text/x-handlebars-template">
    {{#each emojis}}
        <li class="list-inline-item">
            <a class="fn-add-unicode" data-unicode="{{value}}">{{value}}</a>
        </li>
    {{/each}}
</script>