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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

require([
    'jquery',
    'jquery/ui',
    'mage/adminhtml/events',
    'mage/backend/tabs',
    'domReady!'
], function($) {
    'use strict';

    window.initedRefreshDatagenerator = false;

    $('#category-edit-container #category_info_tabs, #product_info_tabs').on('tabscreate tabsactivate', function() {
        if (window.initedRefreshDatagenerator) {
            return;
        }
        window.initedRefreshDatagenerator = true;

        (function refreshDatagenerator()
        {
            var obj = function(param) {
                return $('[id$="custom_commission' + param + '"]');
            };

            var tr = function(param)
            {
                return obj(param).parents('.control').parent();
            };

            obj('s_flat_rate')
                .change(function() {
                    var $this = $(this);
                    switch (parseInt($this.val(), 10)) {
                        case 0:
                            tr('').slideUp(200);
                            break;
                        case 1:
                        case 2:
                            tr('').slideDown(200);
                            break;
                    }
                })
                .change();
        })();
    });

    $('body.catalog-category-edit .entry-edit, body.catalog-product-edit .entry-edit').on('click', '.fieldset-wrapper .fieldset-wrapper-title', function() {
        if (window.initedRefreshDatagenerator) {
            return;
        }
        window.initedRefreshDatagenerator = true;

        (function refreshDatagenerator()
        {
            var obj = function(param) {
                var obj = $('[name="custom_commission' + param + '"]');
                if (!obj.length) {
                    obj = $('[name="product[custom_commission' + param + ']"]');
                }
                return obj;
            };

            var tr = function(param)
            {
                return obj(param).parents('.admin__field');
            };

            obj('s_flat_rate')
                .change(function() {
                    var $this = $(this);
                    switch (parseInt($this.val(), 10)) {
                        case 0:
                            tr('').slideUp(200);
                            break;
                        case 1:
                        case 2:
                            tr('').slideDown(200);
                            break;
                    }
                })
                .change();
        })();
    });
});