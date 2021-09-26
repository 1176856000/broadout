/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */
define([
    "jquery",
    "Plumrocket_Datagenerator/js/editor",
    "Magento_Ui/js/modal/confirm",
    "Plumrocket_Datagenerator/js/lib/codemirror",
    "domReady!"
], function ($, editor, confirm) {
    "use strict";

    $.widget('prdatagenerator.edit', {
        options : {
            templateFieldSelector: '#datagenerator_template_id',
            formSelector : '#edit_form',
            loaderSelector: '#template-loader',
            idFieldSelector : '#datagenerator_id',
            feedTypeSelector : '#datagenerator_type_feed',
            urlKeyFieldSelector: '#datagenerator_url_key',
            urlKeyFieldNoteSelectorAddress: '#url_key-note .address',
            urlKeyFieldNoteSelectorBaseUrl: '#url_key-note .base-url',
            templateEditorTabSelector : '#prdatagenerator_tabs_editor_section',
            initCodeButtonSelector: '#init-codes-button',
            storeViewSelector : '#datagenerator_store_id',
            confirmMessage : 'All current options will be replaced. Are you sure?',
            alreadyLoadedTemplate: false,
            //for templates with this url keys allow select feed type
            templatesForAllFeedTypes : ['feed.csv', 'feed.rss', 'feed.xml']

        },

        _create: function () {
            //change data feed url
            $(this.options.urlKeyFieldSelector).on('change', $.proxy(this._changeUrlKeyAddress, this));
            //each store view can have different url
            //load and update url for store view
            $(this.options.storeViewSelector).on('change', $.proxy(this._updateWebsiteUrl, this));

            //loading selected template
            $(this.options.templateFieldSelector).on('change', $.proxy(this._loadTemplate, this));

            //show/hide filters
            $(this.options.feedTypeSelector).on('change', this._showHideFilterTab);
            this._showHideFilterTab('', $(this.options.feedTypeSelector));

            //inialize editor
            editor.init();
            $(this.options.templateEditorTabSelector).on('click', $.proxy(editor.refresh));
            $(this.options.initCodeButtonSelector).on('click', $.proxy(editor.refresh));

            this.categoryTab = $('#prdatagenerator_tabs_category_section');
            if (this.categoryTab[0].tagName !== 'LI') {
                this.categoryTab = this.categoryTab.parent();
            }
        },

        _updateWebsiteUrl : function (event) {
            var field = event.target,
                storeId = $(field).val();

            if (this.options.storeViews[storeId]) {
                $(this.options.urlKeyFieldNoteSelectorBaseUrl).html(this.options.storeViews[storeId]);
            }
        },

        _changeUrlKeyAddress: function (event) {
            var field = event.target;
            $(this.options.urlKeyFieldNoteSelectorAddress).html($(field).val());
        },

        _loadTemplate : function (event) {
            var field = event.target,
                self = this;
            var templateId = parseInt($('option:selected', field).val());

            if ($(self.options.idFieldSelector).length && $(self.options.idFieldSelector).val() > 0) {
                self.options.alreadyLoadedTemplate = true;
            }

            // if selected blank document
            if (!templateId) {

                confirm({
                    content: self.options.confirmMessage,
                    actions: {

                        /**
                         * Confirmation handler
                         *
                         */
                        confirm: function () {
                            $(self.options.formSelector).find('input, textarea').each(function () {
                                $(this).val('');
                                $(self.options.urlKeyFieldNoteSelectorAddress).html('');
                                editor.update({code_header: '', code_item: '',code_footer: ''});
                                $(self.options.feedTypeSelector).prop('disabled',false);
                                self.options.alreadyLoadedTemplate = false;
                            });
                        },

                        /**
                         * Cancel confirmation handler
                         *
                         */
                        cancel: function () {
                            return false;
                        }
                    }
                });

            } else if (templateId && self.options.alreadyLoadedTemplate) {
                    confirm({
                        content: self.options.confirmMessage,
                        actions: {

                            /**
                             * Confirmation handler
                             *
                             */
                            confirm: function () {
                                self._proccessLoad(self, templateId);
                            }
                        }
                    });

            } else {
                self._proccessLoad(self, templateId);
            }
        },

        _proccessLoad :function (self, templateId) {
            this._loader.show(this);
            $.post(this.options.tempateAction, {template_id : templateId}).done(function (data) {
                self._loader.hide(self);
                if (!data.error) {

                    if (self.options.templatesForAllFeedTypes.indexOf(data.url_key) >= 0) {
                        $(self.options.feedTypeSelector).prop('disabled',false);
                    } else {
                        $(self.options.feedTypeSelector).prop('disabled',true);
                    }

                    self.options.alreadyLoadedTemplate = true;
                    delete data.template_id;
                    delete data.id;
                    delete data.store_id;

                    $(self.options.formSelector).find('select, input, textarea').each(function () {
                        if (data[ $(this).attr('name') ]) {
                            $(this).val(data[ $(this).attr('name') ]).change();
                        }

                        editor.update(data);
                    });
                    if (!$(self.options.feedTypeSelector).hasClass('hasCustomEvent')) {
                        $(self.options.feedTypeSelector).on('change', self._showHideFilterTab);
                    }

                    self.changeVisibilityCategoryTab(data);
                } else {
                    alert(data.error);
                }
            });
        },

        // show\hide loader
        _loader : {
            show : function (self) {
                $(self.options.templateFieldSelector).hide();
                $(self.options.loaderSelector).show();
            },
            hide: function (self) {
                $(self.options.templateFieldSelector).show();
                $(self.options.loaderSelector).hide();
            }
        },

        _showHideFilterTab : function (event, self) {
            var $this;

            if (self) {
                $this= self;
            } else {
                $this = this;
            }
            $($this).addClass('hasCustomEvent');
            if (+$($this, 'option[selected]').val()) {
                $('li[data-ui-id=prdatagenerator-datagenerator-edit-tabs-tab-item-rules-section]').show();
            } else {
                $('li[data-ui-id=prdatagenerator-datagenerator-edit-tabs-tab-item-rules-section]').hide();
            }
        },

        changeVisibilityCategoryTab: function (data) {
            if ('1' === data.show_category_tab) {
                this.categoryTab.show();
            } else {
                this.categoryTab.hide();
            }
        }
    });

    return $.prdatagenerator.edit;
});
