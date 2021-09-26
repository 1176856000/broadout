/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

define(['underscore'], function (_) {
    'use strict';

    /**
     * @param {Object} config
     */
    return {
        /**
         * @param {string} searchConfig
         */
        initSearch: function (searchConfig) {
            searchConfig = JSON.parse(searchConfig);
            var self = this;
            var searchInput = document.getElementById('affiliate-program-search');

            searchInput.addEventListener('input', function (e) {
                var searchTerm = e.target.value;
                if (! searchTerm) {
                    self.showAll();
                    return false;
                }

                searchConfig.forEach(function (affiliateType) {
                    if (self.matchTerm(affiliateType, searchTerm)) {
                        self.show(affiliateType);
                    } else {
                        self.hide(affiliateType);
                    }
                });
            });
        },

        /**
         * @param {{id: number, name: string, metaKeywords: string[]}} affiliateType
         * @param {string} searchTerm
         */
        matchTerm: function (affiliateType, searchTerm) {
            searchTerm = searchTerm.toLowerCase();

            if (0 === affiliateType.metaKeywords.length) {
                return affiliateType.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1;
            }

            return _.any(affiliateType.metaKeywords, function (metaKeyword) {
                return metaKeyword.indexOf(searchTerm.toLowerCase()) !== -1;
            });
        },

        /**
         * @param {{id: number, name: string}} affiliateType
         */
        show: function (affiliateType) {
            this.showElement(document.querySelector(
                `#edit_form label[for=affiliate_type_${affiliateType.id}]`
            ));
        },

        /**
         * @param {{id: number, name: string}} affiliateType
         */
        hide: function (affiliateType) {
            this.hideElement(document.querySelector(
                `#edit_form label[for=affiliate_type_${affiliateType.id}]`
            ));
        },

        showAll: function () {
            _.map(document.querySelectorAll('#edit_form label'), this.showElement.bind(this))
        },

        /**
         * @param {HTMLElement} element
         */
        showElement: function (element) {
            element.parentElement.classList.remove('pr-affiliate-hidden')
        },

        /**
         * @param {HTMLElement} element
         */
        hideElement: function (element) {
            element.parentElement.classList.add('pr-affiliate-hidden')
        },
    };
});
