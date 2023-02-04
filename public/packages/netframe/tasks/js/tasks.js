(function () {
    'use strict';

    function Tasks(options) {
        this.$wrapper = options.$wrapper;
        this.$projectId = options.$projectId;
    }

    Tasks.prototype.listenEvent = function () {
        var that = this;

        $(document).on('submit', 'form.fn-add-file', function (e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            that.addFile($(this));
        });

        /*
         * Close modal-files hook
         */
        $(document).on('hidden.bs.modal', '#modal-files', function (e) {
            //get all uploaded files and implement in file explorer
            var filesForm = $(this).find('form.fn-add-file');

            // check if form update
            var checkUpdate = filesForm.find("input[name='idFile']");

            if (filesForm.length != 0 && checkUpdate.length == 0) {
                filesForm.submit();
            }

            //$(this).data('bs.modal', null);
            //$(this).find(".modal-body").empty()
        });

        //that.$wrapper.on('click', '.add-row', function () {
        $(document).on('click', '.add-row', function () {
            // $('.postrows').before("<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>");
            var dataId = { id: 0, project: that.$projectId };
            var jqXhr = $.post(laroute.route('task.addTaskCol'), {
                postData: dataId
            });
            jqXhr.success(function (data) {
                if (data.inserted) {
                    that.$wrapper.find('.postrows').before(data.body);
                    that.table();
                }
            });
            return false
        });

        that.$wrapper.on('click', '.delete-el', function (e) {
            var _confirm = confirm(trans('task.confirmDelete'));

            if (!_confirm) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            else {
                e.preventDefault();
                var el = $(this);
                var panel = el.closest(".task");
                var dataId = { type: el.data('type'), id: el.data('id') };

                var jqXhr = $.post(laroute.route('task.delete'), {
                    postData: dataId
                });

                jqXhr.success(function (data) {
                    if (dataId) {
                        panel.fadeOut();
                    }
                });
            }
            return false;
        });

        that.$wrapper.on('click', '.duplicate-el', function (e) {
            var _confirm = confirm(trans('task.confirmDuplicate'));

            if (!_confirm) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            else {
                e.preventDefault();
                var el = $(this);
                var panel = el.closest(".task");
                var dataId = { id: el.data('id') };

                var jqXhr = $.post(laroute.route('task.duplicate'), {
                    postData: dataId
                });

                jqXhr.success(function (data) {
                    if (data.inserted) {
                        panel.find('.menu-wrapper').removeClass('active');
                        var row = panel.clone();
                        //panel.after(row.html().replace('data-id="'+el.data("id")+'"', 'data-id="'+data.id+'"'));
                        panel.after(data.body);
                        that.table();
                    }
                });
            }
            return false;
        });

    };

    Tasks.prototype.table = function () {
        var that = this;

        that.$wrapper.find('.editable').on('click', function (e) {
            if (!$(e.target).is('input')) {
                var $this = $(this);
                var $init = $this.text().replace(/\t/g, '');
                if ($this.data('value'))
                    $init = $this.data('value');
                var $custom = "no";
                if ($this.data('custom'))
                    $custom = $this.data('custom');
                var $reset = false;
                if ($this.data('type') != 'file') {
                    var $input = $('<input>', {
                        value: $init,
                        type: $this.data('type'),
                        class: 'form-control',
                        onkeydown: function () {
                            if ($this.data('type') == 'date') {
                                return "return false";
                            }
                        },
                        blur: function () {
                            if ($reset) $this.text($init);
                            else {
                                if ($this.data('type') == 'date') {
                                    var parts = this.value.split("-");
                                    $this.html('<span class="field-c">' + parts[2] + '/' + parts[1] + '/' + parts[0] + '</span>');
                                }
                                else
                                    $this.html('<span class="field-c">' + this.value + '</span>');

                            }
                        },
                        change: function () {
                            //  || ($this.data('type') == 'date' && !moment(this.value, "DD/MM/YYYY", true).isValid())

                            if (!$input.is(':valid')) {
                                $input.addClass("is-invalid")
                                $this.append("<span style='position: absolute left: 13px; top: 5px;' class='invalid-feedback'>" + trans('task.invalid') + "</span>");
                                $input.focus();
                            }
                            else {
                                var dataId = { field: $this.data('field'), id: $this.data('id'), custom: $custom, value: this.value, project: currentOpenProject };
                                var jqXhr = $.post(laroute.route('task.addTaskCol'), {
                                    postData: dataId
                                });
                                jqXhr.success(function (data) {
                                    if (!data.inserted) {
                                        $reset = true;
                                    } else {
                                        $init = '<span class="field-c">' + data.value + '</span>';
                                        // if($this.data('type')=='date')
                                        //  $init = (new Date(this.value)).toString();
                                        // $this.data('value', data.value);
                                    }
                                });
                                $input.blur();
                            }
                        },
                        keyup: function (e) {
                            if (e.which === 13) {
                                if (!$input.is(':valid')) {
                                    $input.addClass("is-invalid")
                                    $this.append("<span style='position: absolute; left: 13px; top: 5px;' class='invalid-feedback'>" + trans('task.invalid') + "</span>");
                                }
                                else {
                                    var dataId = { field: $this.data('field'), id: $this.data('id'), custom: $custom, value: this.value, project: currentOpenProject };
                                    var jqXhr = $.post(laroute.route('task.addTaskCol'), {
                                        postData: dataId
                                    });
                                    jqXhr.success(function (data) {
                                        if (!data.inserted) {
                                            $reset = true;
                                        } else {
                                            $init = data.value;
                                            // if($this.data('type')=='date')
                                            //  $init = (new Date(this.value)).toString();
                                            // $this.data('value', data.value);
                                        }
                                    });
                                    $input.blur();
                                }
                            }
                            if (e.which === 27) {
                                $reset = true;
                                $input.blur();
                            }
                        }
                    }).appendTo($this.empty()).focus();
                }
                else if ($this.data('type') == 'file') {
                    // manage file upload on row
                    var taskId = $this.data('id');
                    var urlMedia = $('.fn-add-file').attr('href') + '&taskId=' + taskId;
                    var modalTarget = $('.fn-add-file').data('target');
                    $(modalTarget + ' .modal-body').load(urlMedia);
                    $(modalTarget).modal('show');
                }
                else {

                }
            }
        });

        that.$wrapper.find('.selectable').on('click', function (e) {
            if (!$(e.target).is('select')) {
                var $this = $(this);
                var $init = $this.text();
                var $reset = false;
                var $input = $('<select>', {
                    option: { value: 1, text: $this.text() },
                    class: 'form-control',
                    blur: function () {
                        if (this.value == 1) $this.html('<span class="statut alert-success">' + trans('task.state.complete') + '</span>');
                        else if (this.value == 2) $this.html('<span class="statut alert-danger">' + trans('task.state.todo') + '</span>');
                        else $this.html('<span class="statut alert-warning">' + trans('task.state.progress') + '</span>');
                    },
                    change: function () {
                        var dataId = { field: $this.data('field'), id: $this.data('id'), value: this.value, project: that.$projectId };
                        var jqXhr = $.post(laroute.route('task.addTaskCol'), {
                            postData: dataId
                        });
                        jqXhr.success(function (data) {
                            if (!data.inserted) {
                                $reset = true;
                            }
                        });
                        $input.blur();
                    }
                }).append($('<option />')
                    .text(trans('task.state.progress'))
                    .val('0'),
                    $('<option />')
                        .text(trans('task.state.complete'))
                        .val('1'),
                    $('<option />')
                        .text(trans('task.state.todo'))
                        .val('2'),
                ).appendTo($this.empty()).focus();
            }
        });
    };

    Tasks.prototype.addFile = function (form) {
        var that = this;

        var modalId = '#modal-files';
        var modalContent = $(modalId + ' .modal-content');
        var actionUrl = form.attr('action');
        var formData = form.find('input, hidden, select, textarea, radio, checkbox').serializeArray();

        // add data to object array serialized json
        formData.push({
            name: "httpReferer",
            value: requestUrl,
        });
        /*
        formData.push({
            name: "idFolder",
            value: that.$idFolder,
        });
        */
        formData.push({
            name: "fromTasks",
            value: true,
        });

        $.ajax({
            url: actionUrl,
            data: formData,
            type: "POST",
            success: function (data) {
                //$(modalId).find('.modal-content').html(data.view);

                if (typeof data.replaceContent != 'undefined') {
                    var elTarget = $(data.targetId);
                    elTarget.fadeOut('slow', function () {
                        elTarget.replaceWith(data.viewContent);
                        elTarget.fadeIn('slow');
                    });
                }

                // if error
                if (typeof data.error != 'undefined') {
                    // reload tags
                }

                // if workflow
                if (typeof data.withWorkflow != 'undefined' && data.withWorkflow == 1 && typeof data.workflows != 'undefined') {
                    // call workflow lines
                    $.each(data.workflows, function (i, wf) {
                        // add row and sub rows
                        var existingTaskRow = data.attachExistingTask;
                        that.addRow(wf.wfId, wf.fileId, existingTaskRow);
                    });
                }

                // wait for variable closeModal to TRUE from php script
                if (typeof data.closeModal != 'undefined') {
                    $(modalId).modal('hide');
                }

                // wait for variable waitCloseModal to TRUE from php script to close modal after delay
                if (typeof data.waitCloseModal != 'undefined') {
                    setTimeout(function () {
                        $(modalId).modal('hide');
                    }, data.waitCloseModal);
                }
            },
            error: function (textStatus, errorThrown) {
                //console.log(textStatus);
            }
        });
    };

    Tasks.prototype.addRow = function (workflowId, fileId, existingTaskRow) {
        var that = this;
        var dataId = { id: 0, project: that.$projectId };
        var jqXhr = $.post(laroute.route('task.addTaskCol'), {
            postData: dataId,
            workflowId: workflowId,
            fileId: fileId,
            existingTaskRow: existingTaskRow
        });
        jqXhr.success(function (data) {
            if (data.inserted) {
                that.$wrapper.find('.postrows').before(data.body);
                that.table();
            }
            if (data.updated) {
                that.$wrapper.find('tr[data-main-task-id="task-' + data.id + '"]').replaceWith(data.body);
                that.table();
            }
        });
        return false

    };

    window.Tasks = function (options) {
        var tasks = new Tasks(options);
        tasks.listenEvent();

        return tasks;
    };

})();
