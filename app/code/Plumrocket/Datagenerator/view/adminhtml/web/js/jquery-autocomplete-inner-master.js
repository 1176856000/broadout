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
define(["jquery"], function ($) {
    "use strict";

    return {

        // Defines for the example the match to take which is any word (with Umlauts!!).
        init : function () {

            var self = this;

            $('.CodeMirror input').each(function () {

                var block_id = $(this).parent().parent().prev().attr('id');

                $(this).autocomplete({
                    position: { my : "left top", at: "left bottom" },
                    source: function (request, response) {
                        var str = self._leftMatch(request.term, $(this).get(0));
                        str = (str != null) ? str[0] : "";
                        response($.ui.autocomplete.filterGroups(nodes, str, block_id == 'code_item'));
                    },
                    //minLength: 2,  // does have no effect, regexpression is used instead
                    focus: function () {
                        // prevent value inserted on focus
                        return false;
                    },
                    // Insert the match inside the ui element at the current position by replacing the matching substring
                    select: function (event, ui) {
                        var m = self._leftMatch(this.value, this);
                        if (m != null) {
                            m = m[0];
                            var beg = this.value.substring(0, this.selectionStart - m.length);

                            this.value = beg + ui.item.value + this.value.substring(this.selectionStart, this.value.length);
                            var pos = beg.length + ui.item.value.length;
                            self._setCursorPosition(this, pos);
                            return false;
                        }
                    },
                    search:function (event, ui) {
                        var m = self._leftMatch(this.value, this);
                        return (m != null )
                    },
                    open: function (event) {
                        autocompleteAreasOpened = true;
                    },
                    close: function (event) {
                        autocompleteAreasOpened = false;
                    }
                })
                .data("autocomplete")._renderItem = function ( ul, item ) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.label + "</a>")
                        .appendTo(ul);
                };
            });


            $.ui.autocomplete.filterGroups = function (array, term, show_all) {
                var result = [];

                if (show_all) {

                    //getting sellected type of feed
                    var feed_type = $('#datagenerator_type_feed').val();

                    //for feed type, such as "product" (val 1)
                    if (feed_type == '1') {

                        var _productItems = $.ui.autocomplete.filter(array['product'], term);

                        if (_productItems.length > 0) {
                            result = [ {'label': '<strong>Product</strong>', 'value': '{product.', 'head': true} ];
                            result = result.concat(_productItems);
                        }

                        var curr_pos = itemEditor.getCursor();
                        var text = itemEditor.getRange({line: 0, ch: 0}, curr_pos);

                        var count_begins = text.match(/\{product.child\}/g) || [];
                        var count_ends = text.match(/\{\/product.child\}/g) || [];

                        if (count_begins.length > count_ends.length) {
                            var _childItems = $.ui.autocomplete.filter(array['child'], term);
                            if (_childItems.length > 0) {
                                result = result.concat([ {'label': '<strong>Child</strong>', 'value': '{child.', 'head': true} ]);
                                result = result.concat(_childItems);
                            }
                        }
                    }

                    var _categoryItems = $.ui.autocomplete.filter(array['category'], term);
                    if (_categoryItems.length > 0) {
                        result = result.concat([ {'label': '<strong>Category</strong>', 'value': '{category.', 'head': true} ]);
                        result = result.concat(_categoryItems);
                    }
                }

                var _siteItems = $.ui.autocomplete.filter(array['site'], term);
                if (_siteItems.length > 0) {
                    result = result.concat([ {'label': '<strong>Site</strong>', 'value': '{site.', 'head': true} ]);
                    result = result.concat(_siteItems);
                }

                return result;
            };

            // Overrides the default autocomplete filter function to search only from the beginning of the string
            $.ui.autocomplete.filter = function (array, term) {
                // prevent dublicate
                term = term.replace('{', '');

                var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex('{' + term), "i");
                return $.grep(array, function (value) {
                    return matcher.test('{' + value.label || '') || matcher.test(value.value || '') || matcher.test(value || '');
                });
            };
        },

        _leftMatch : function (string, area) {
            return string.substring(0, area.selectionStart).match(/[\{]{1}[\w\.]+$/)
        },

        _setCursorPosition : function (area, pos) {
            if (area.setSelectionRange) {
                area.setSelectionRange(pos, pos);
            } else if (area.createTextRange) {
                var range = area.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        }

    }
});
