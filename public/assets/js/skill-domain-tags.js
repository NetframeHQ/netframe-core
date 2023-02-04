/**
 * Plugin for skill domain and tags.
 */
$(function () {

    function Tags(options) {
        this.skillSelector = options.skillSelector;
        this.domainSelector = options.domainSelector;
        this.tagsSelector = options.tagsSelector;
        this.domains = [];
        this.tags = [];
        this.baseUrl = options.baseUrl;
        this.initialTags = options.initialTags;
    }

    Tags.prototype.initializeSkills = function () {
        var that = this;

        this.skillSelector.select2({width: 'resolve'})
            .on('change', function () {
                that.disableDomains();

                that.fetchDomains().done(function () {
                    that.enableDomains();
                });
            });
    };

    Tags.prototype.initializeDomains = function () {
        var that = this;

        this.domainSelector.select2({
            width: 'resolve',
            data: function () {
                return {results: that.domains};
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

                var domainId = that.domainSelector.val();
                var route = that.baseUrl + laroute.route('domain_tag_create', {domainId: domainId});

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
        this.initializeSkills();
        this.initializeDomains();
        this.initializeTags();

        if (!this.skillSelector.val()) {
            this.disableDomains();
        }

        if (!this.domainSelector.val()) {
            this.disableTags();
        }
    };

    Tags.prototype.disableDomains = function () {
        this.domainSelector.select2('enable', false);
        this.domainSelector.select2('data', null);
    };

    Tags.prototype.enableDomains = function () {
        this.domainSelector.select2('enable', true);
    };

    Tags.prototype.disableTags = function () {
        this.tagsSelector.select2('enable', false);
        this.tagsSelector.select2('data', null);
    };

    Tags.prototype.enableTags = function () {
        this.tagsSelector.select2('enable', true);
    };

    Tags.prototype.fetchDomains = function () {
        this.domains = [];

        var that = this;
        var skillId = this.skillSelector.val();
        var route = this.baseUrl + laroute.route('skill_domains_all', {skillId: skillId});

        return $.get(route, function (data) {
            $.each(data, function (index, category) {
                that.domains.push({
                    id: category.id,
                    text: category.name
                })
            })
        });
    };

    Tags.prototype.fetchTags = function (query) {
        var that = this;

        this.tags = [];
        var domainId = this.domainSelector.val();
        var route = this.baseUrl + laroute.route('domain_tags_autocomplete', {domainId: domainId, q: query});

        return $.get(route, function (data) {
            that.tags = [];

            $.each(data, function (index, tag) {
                that.tags.push({
                    id: tag.id,
                    text: tag.name
                })
            })
        });
    };

    Tags.prototype.setup = function () {
        var that = this;
        var skillId = that.skillSelector.val();

        if (!skillId) {
            that.initializeSelects();
        } else {
            that.fetchDomains().done(function () {
                that.initializeSelects();
            });
        }
    };

    if (!window.Netframe) {
        window.Netframe = {};
    }

    // Publish the object
    window.Netframe.SkillDomainTags = function (options) {
        var tags = new Tags(options);
        tags.setup();
    }

});
