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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'languageField'
], function ($, _, mageTemplate, languageField) {
    'use strict';

    $.widget('plum.quickSearch', {
        options: {
            autocomplete: 'off',
            minSearchLength: 2,
            responseFieldElements: 'ul li',
            selectClass: 'selected',
            template:
                '<li class="<%- data.row_class %>" role="option">' +
                '<span class="qs-option-name">' +
                ' <%- data %>' +
                '</span>' +
                '</li>',
            suggestionDelay: 300
        },

        /** @inheritdoc */
        _create: function () {
            this.responseList = {
                indexList: null,
                selected: null
            };

            this.autoComplete = $(this.options.destinationSelector.replace('{id}', this.element.get(0).dataset.id));

            _.bindAll(this, '_onPropertyChange');
            this.element.attr('autocomplete', this.options.autocomplete);

            this.element.on('blur', $.proxy(function () {
                setTimeout($.proxy(function () {
                    if (this.autoComplete.is(':visible')) {
                        this.element.trigger('focus');
                    }
                    this.autoComplete.hide();
                }, this), 250);
            }, this));

            // Prevent spamming the server with requests by waiting till the user has stopped typing for period of time
            this.element.on('input propertychange', _.debounce(this._onPropertyChange, this.options.suggestionDelay));
        },

        /**
         * Executes when the value of the search input field changes. Executes a GET request
         * to populate a suggestion list based on entered text. Handles click (select), hover,
         * and mouseout events on the populated suggestion list dropdown.
         * @private
         */
        _onPropertyChange: function () {
            var searchField = this.element,
                clonePosition = {
                    position: 'absolute',
                    width: searchField.outerWidth()
                },
                source = this.options.template,
                template = mageTemplate(source),
                dropdown = $('<ul role="listbox"></ul>'),
                value = this.element.val();

            if (value.length >= parseInt(this.options.minSearchLength, 10)) {
                $.getJSON(this.options.url, {
                    q: value,
                    language: this.getLanguageCode()
                }, $.proxy(function (data) {
                    if (data.length) {
                        $.each(data, function (index, element) {
                            var html;

                            html = template({
                                data: element
                            });
                            dropdown.append(html);
                        });

                        this.responseList.indexList = this.autoComplete.html(dropdown)
                            .css(clonePosition)
                            .show()
                            .find(this.options.responseFieldElements + ':visible');

                        this.element.removeAttr('aria-activedescendant');

                        this.responseList.indexList
                            .on('click', function (e) {
                                this.responseList.selected = $(e.currentTarget);
                                this.element.val(this.responseList.selected.text());
                                this.element.change();
                            }.bind(this));
                    }
                }, this));
            }
        },

        getLanguageCode: function () {
            return languageField.getCurrentLanguage();
        },
    });

    return $.plum.quickSearch;
});

