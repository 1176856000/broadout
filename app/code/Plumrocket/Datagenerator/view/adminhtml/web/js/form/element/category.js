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

/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/abstract',
    'plumSearch',
    'languageField'
], function (Abstract, search, languageField) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Plumrocket_Datagenerator/form/element/autocomplete',
        },

        initialize: function () {
            this._super();
            languageField.setCurrentLanguage('en-US');
        },

        onElementRender: function (element, uiClass) {
            setTimeout(function () {
                search({
                    url: uiClass.url,
                    destinationSelector: 'div#search_autocomplete_' + uiClass.uid + '.search-autocomplete'
                }, element);
            }.bind(this), 300);
        }
    });
});
