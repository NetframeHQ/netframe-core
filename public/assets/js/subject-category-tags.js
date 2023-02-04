/**
 * Manage subjects, categories and tags.
 */
$(function () {

    function Tags(options) {
        this.subjectSelector = options.subjectSelector;
        this.categorySelector = options.categorySelector;
        this.tagsSelector = options.tagsSelector;
        this.categories = [];
        this.tags = [];
        this.baseUrl = options.baseUrl;
        this.initialTags = options.initialTags;
    }

    Tags.prototype.initializeSubjects = function () {
        var that = this;

        this.subjectSelector.select2({width: 'resolve'})
            .on('change', function () {
                that.disableCategories();

                that.fetchCategories().done(function () {
                    that.enableCategories();
                });
            });
    };

    Tags.prototype.initializeCategories = function () {
        var that = this;

        this.categorySelector.select2({
            width: 'resolve',
            data: function () {
                return {results: that.categories};
            }
        }).on('change', function () {
            that.disableTags();

            that.fetchTags().done(function () {
                that.enableTags();
            });
        });
    };

    Tags.prototype.initializeTags = function () {
        var that = this;

        this.tagsSelector.select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [','],
            multiple: true,
            minimumInputLength: 3,

            query: function (query) {
                that.fetchTags(query.term).done(function () {
                    query.callback({results: that.tags});
                });
            },

            createSearchChoice: function (term, data) {
                if ($(data).filter(function () {
                        return this.text.localeCompare(term) === 0;
                    }).length === 0) {
                    return {
                        id: 'id' + term,
                        text: term
                    };
                }
            },

            initSelection: function (element, callback) {
                var data = [];

                $(element.val().split(',')).each(function () {
                    $.each(that.initialTags, function (index, tag) {
                        return data.push({id: tag.id, text: tag.name});
                    });
                });

                callback(data);
            }
        });

        this.tagsSelector.on('select2-selecting', function (e) {
            var tag = e.choice;

            // Create the new tag
            if (tag.id === 'id' + tag.text) {
                e.preventDefault();

                var categoryId = that.categorySelector.val();
                var route = that.baseUrl + laroute.route('category_tag_create', {categoryId: categoryId});

                $.post(route, {name: tag.text})
                    .done(function (data) {
                        tag.id = data.id;

                        // Update the select with the new tag
                        var selectData = that.tagsSelector.select2('data');
                        selectData.push(tag);
                        that.tagsSelector.select2('data', selectData, true);
                        that.tagsSelector.select2('close');
                    })
                ;
            }
        });
    };

    Tags.prototype.initializeSelects = function () {
        this.initializeSubjects();
        this.initializeCategories();
        this.initializeTags();

        if (!this.subjectSelector.val()) {
            this.disableCategories();
        }

        if (!this.categorySelector.val()) {
            this.disableTags();
        }
    };

    Tags.prototype.disableCategories = function () {
        this.categorySelector.select2('enable', false);
        this.categorySelector.select2('data', null);
    };

    Tags.prototype.enableCategories = function () {
        this.categorySelector.select2('enable', true);
    };

    Tags.prototype.disableTags = function () {
        this.tagsSelector.select2('enable', false);
        this.tagsSelector.select2('data', null);
    };

    Tags.prototype.enableTags = function () {
        this.tagsSelector.select2('enable', true);
    };

    Tags.prototype.fetchCategories = function () {
        this.categories = [];

        var that = this;
        var subjectId = this.subjectSelector.val();
        var route = this.baseUrl + laroute.route('subject_categories_all', {subjectId: subjectId});

        return $.get(route, function (data) {
            $.each(data, function (index, category) {
                that.categories.push({
                    id: category.id,
                    text: category.name
                })
            })
        });
    };

    Tags.prototype.fetchTags = function (query) {
        var that = this;
        this.tags = [];
        var categoryId = this.categorySelector.val();
        var route = this.baseUrl + laroute.route('category_tags_autocomplete', {categoryId: categoryId, q: query});

        return $.get(route, function (data) {
            that.tags = [];

            $.each(data, function (index, tag) {
                that.tags.push({
                    id: tag.id,
                    text: tag.name
                })
            });
        });
    };

    Tags.prototype.setup = function () {
        var that = this;
        var subjectId = that.subjectSelector.val();

        if (!subjectId) {
            that.initializeSelects();
        } else {
            that.fetchCategories().done(function () {
                that.initializeSelects();
            });
        }
    };

    if (!window.Netframe) {
        window.Netframe = {};
    }

    // Publish the object
    window.Netframe.SubjectCategoryTags = function (options) {
        var tags = new Tags(options);
        tags.setup();
    }

});
