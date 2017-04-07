/*jshint browser:true jquery:true*/
define([
    "jquery",
    "Magento_Ui/js/modal/prompt",
    "Magento_Ui/js/modal/confirm",
    "Magento_Ui/js/modal/alert",
    "Magento_Ui/js/modal/modal",
    "jquery/ui",
    "jquery/jstree/jquery.jstree",
    "jquery/file-uploader",
    "mage/mage",
    "legacy-build"
], function($, prompt, confirm, alert){

    window.bannerMediabrowserUtility = {
        windowId: 'modal_dialog_message',
        openDialog: function(url, width, height, title, options) {
            var windowId = this.windowId,
                content = '<div class="popup-window magento_message" "id="' + windowId + '"></div>',
                self = this;

            if (this.modal) {
                //this.modal.html($(content).html());
            } else {
                this.modal = $(content).modal($.extend({
                    title:  title || 'Insert File...',
                    modalClass: 'magento',
                    type: 'slide',
                    buttons: []
                }, options));

                $.ajax({
                    url: url,
                    cash:true,
                    data: {form_key: window.FORM_KEY},
                    type: 'POST',
                    context: $(this),
                    showLoader: true

                }).done(function(data) {
                    self.modal.html(data).trigger('contentUpdated');
                });


            }
            this.modal.modal('openModal');

        },
        closeDialog: function() {
            this.modal.modal('closeModal');
        }
    };

    $.widget("mage.mediabrowser", {
        eventPrefix: "mediabrowser",
        options: {
            targetElementId: null,
            contentsUrl: null,
            onInsertUrl: null,
            newFolderUrl: null,
            deleteFolderUrl: null,
            deleteFilesUrl: null,
            headerText: null,
            tree: null,
            currentNode: null,
            storeId: null,
            showBreadcrumbs: null,
            hidden: 'no-display'
        },
        /**
         * Proxy creation
         * @protected
         */
        _create: function() {
            this._on({
                'click [data-row=file]': 'selectFile',
                'dblclick [data-row=file]': 'insert',
                'click #new_folder': 'newFolder',
                'click #delete_folder': 'deleteFolder',
                'click #delete_files': 'deleteFiles',
                'click #insert_files': 'insertSelectedFiles',
                'fileuploaddone': '_uploadDone',
                'click [data-row=breadcrumb]': 'selectFolder'
            });
            this.activeNode = null;
            //tree dont use event bubbling
            this.tree = this.element.find('[data-role=tree]');
            this.tree.on("select_node.jstree", $.proxy(this._selectNode, this));
        },

        _selectNode: function(event, data) {
            var node = data.rslt.obj.data('node');
            this.activeNode = node;
            this.element.find('#delete_files, #insert_files').toggleClass(this.options.hidden, true);
            this.element.find('#contents').toggleClass(this.options.hidden, false);
            this.element.find('#delete_folder').toggleClass(this.options.hidden, node.id == 'root');
            this.element.find('#content_header_text').html(node.id == 'root' ? this.headerText : node.text);

            this.drawBreadcrumbs(data);
            this.loadFileList(node);
        },

        reload : function() {
            return this.loadFileList(this.activeNode);
        },

        loadFileList: function(node) {
            var contentBlock = this.element.find('#contents');
            return $.ajax({
                url: this.options.contentsUrl,
                type: 'GET',
                dataType: 'html',
                data: {
                    form_key: FORM_KEY,
                    node: node.id
                },
                context: contentBlock,
                showLoader: true
            }).done(function(data) {
                contentBlock.html(data).trigger('contentUpdated');
            });
        },

        selectFolder: function(event) {
            this.element.find('[data-id="'+ $(event.currentTarget).data('node').id +'"]>a').click();
        },

        insertSelectedFiles: function() {
            this.element.find('[data-row=file].selected').trigger('dblclick');
        },

        selectFile: function(event) {
            var fileRow = $(event.currentTarget);
            fileRow.toggleClass('selected');
            this.element.find('[data-row=file]').not(fileRow).removeClass('selected');
            this.element.find('#delete_files, #insert_files')
                .toggleClass(this.options.hidden, !fileRow.is('.selected'));
            fileRow.trigger('selectfile');
        },

        _uploadDone: function() {
            this.element.find('.file-row').remove();
            this.reload();
        },

        insert: function(event) {

            // debugger;
            var relpath = $(event.currentTarget.innerHTML).find('img').attr('src').split('.thumbs/').pop().split('?')[0],
                imgThumb = $(event.currentTarget.innerHTML).find('img')[0],
                addImageTothis = $('.addImageTothis');

            addImageTothis.closest('tr').find('img').attr('src', imgThumb.src);
            addImageTothis.closest('tr').find('.imagerelpath').val(relpath);
            addImageTothis.removeClass('addImageTothis');

            $('body').trigger('map_image_collection_JSON');

            window.bannerMediabrowserUtility.closeDialog();

        },

        newFolder: function() {
            var self = this;

            prompt({
                title: this.options.newFolderPrompt,
                actions: {
                    confirm: function (folderName) {
                        return $.ajax({
                            url: self.options.newFolderUrl,
                            dataType: 'json',
                            data: {
                                name: folderName,
                                node: self.activeNode.id,
                                store: self.options.storeId,
                                form_key: FORM_KEY
                            },
                            context: self.element,
                            showLoader: true
                        }).done($.proxy(function(data) {
                            if (data.error) {
                                alert({
                                    content: data.message
                                });
                            } else {
                                self.tree.jstree('refresh',  self.element.find('[data-id="' + self.activeNode.id + '"]'));
                            }
                        }, this));
                    }
                }
            });
        },

        deleteFolder: function() {
            var self = this;

            confirm({
                content: this.options.deleteFolderConfirmationMessage,
                actions: {
                    confirm: function () {
                        return $.ajax({
                            url: self.options.deleteFolderUrl,
                            dataType: 'json',
                            data: {
                                node: self.activeNode.id,
                                store: self.options.storeId,
                                form_key: FORM_KEY
                            },
                            context: self.element,
                            showLoader: true
                        }).done($.proxy(function() {
                            self.tree.jstree('refresh', self.activeNode.id);
                        }, this));
                    },
                    cancel: function () {
                        return false;
                    }
                }
            });
        },

        deleteFiles: function() {
            var self = this;

            confirm({
                content: this.options.deleteFileConfirmationMessage,
                actions: {
                    confirm: function () {
                        var selectedFiles = self.element.find('[data-row=file].selected');
                        var ids = selectedFiles.map(function() {
                            return $(this).attr('id');
                        }).toArray();

                        return $.ajax({
                            url: self.options.deleteFilesUrl,
                            data: {
                                files: ids,
                                store: self.options.storeId,
                                form_key: FORM_KEY
                            },
                            context: self.element,
                            showLoader: true
                        }).done($.proxy(function() {
                            self.reload();
                        }, this));
                    },
                    cancel: function () {
                        return false;
                    }
                }
            });
        },

        drawBreadcrumbs: function(data) {
            if (this.element.find('#breadcrumbs').length) {
                this.element.find('#breadcrumbs').remove();
            }
            var node = data.rslt.obj.data('node');
            if (node.id == 'root') {
                return;
            }
            var breadcrumbs = $('<ul class="breadcrumbs" id="breadcrumbs" />');
            $(data.rslt.obj.parents('[data-id]').get().reverse()).add(data.rslt.obj).each(function(index, element){
                var node = $(element).data('node');
                if (index > 0) {
                    breadcrumbs.append($('<li>\/</li>'));
                }
                breadcrumbs.append($('<li />').data('node', node).attr('data-row', 'breadcrumb').text(node.text));

            });

            breadcrumbs.insertAfter(this.element.find('#content_header'))
        }
    });
});
