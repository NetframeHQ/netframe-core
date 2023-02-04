const SearchBar = new autoComplete({
    data: {
      src: async () => {
        var query = document.querySelector("#search-input").value;
        var headers = new Headers();
        headers.append("Accept", "application/json");
        var init = { method: 'GET', headers: headers };
        var source = await fetch(`/search?query=${query}`, init);
        var data = await source.json();
        return data;
      },
      key: ["label"],
      cache: false
    },
    selector: "#search-input",
    observer: true,
    threshold: 1,
    debounce: 300,
    searchEngine: (query, record) => { return record; },
    resultsList: {
        destination: "#search-input",
        position: "afterend",
        element: "ul",
        className: "nf-autocomplete"
    },
    maxResults: 5,
    resultItem: {
        content: (data, source) => {
            source.innerHTML = `
              ${data.value.thumb}
              <span class="text">${data.match}</span>
            `;
        },
        element: "li",
        highlight: true
    },
    noResults: (dataFeedback, generateList) => {
        generateList(autoCompleteJS, dataFeedback, dataFeedback.results);
        const result = document.createElement("li");
        result.setAttribute("class", "no_result");
        result.setAttribute("tabindex", "1");
        result.innerHTML = `<span>Found No Results for "${dataFeedback.query}"</span>`;
        document.querySelector(`#${autoCompleteJS.resultsList.idName}`).appendChild(result);
    },
    onSelection: feedback => {
        window.document.location = feedback.selection.value.value; 
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                autoCompleteJS.input.value = selection;
            }
        }
    }
});
