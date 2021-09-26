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

require([
    'jquery',
    'mage/translate',
    'extjs/ext-tree'
],function ($) {
    'use strict';

    Ext.tree.TreeNodeUI.prototype.render = function (_25) {
        var n = this.node;
        var _27 = n.parentNode ? n.parentNode.ui.getContainer() : n.ownerTree.container.dom;

        if (!this.rendered) {
            this.rendered = true;
            var a = n.attributes, taxonomyValue = this.googleTaxonomyValues[n.id];
            this.indentMarkup = "";
            if (n.parentNode) {
                this.indentMarkup = n.parentNode.ui.getChildIndent();
            }

            var buf = [
                "<li class=\"x-tree-node\"><div class=\"x-tree-node-el ",
                n.attributes.cls, "\">", "<span class=\"x-tree-node-indent\">",
                this.indentMarkup, "</span>",
                "<img src=\"", this.emptyIcon, "\" class=\"x-tree-ec-icon\">",
                "<img src=\"", a.icon || this.emptyIcon, "\" class=\"x-tree-node-icon",
                (a.icon ? " x-tree-node-inline-icon" : ""),
                (a.iconCls ? " " + a.iconCls : ""),
                "\" unselectable=\"on\">",
                "<a hidefocus=\"on\" href=\"",
                a.href ? a.href : "#", "\" tabIndex=\"1\" ",
                a.hrefTarget ? " target=\"" + a.hrefTarget + "\"" : "",
                "><span unselectable=\"on\">", n.text, "</span></a>",
                "<span class='mapped-text'>", " → ", $.mage.__('mapped as:'), "</span>",
                "<div class='prdatagenerator-field-wrapper'><input data-id='" + n.id + "' type='text' class='mapping_input' name='category_mapping[" + n.id + "]' value='",
                (taxonomyValue ? taxonomyValue : '') + "'/><div id='search_autocomplete_" + n.id + "' class='search-autocomplete'></div></div></div>",
                "<ul class=\"x-tree-node-ct\" style=\"display:none;\"></ul>",
                "</li>"
            ];

            if (_25 !== true && n.nextSibling && n.nextSibling.ui.getEl()) {
                this.wrap = Ext.DomHelper.insertHtml("beforeBegin", n.nextSibling.ui.getEl(), buf.join(""));
            } else {
                this.wrap = Ext.DomHelper.insertHtml("beforeEnd", _27, buf.join(""));
            }
            this.elNode = this.wrap.childNodes[0];
            this.ctNode = this.wrap.childNodes[1];

            var cs = this.elNode.childNodes;
            this.indentNode = cs[0];
            this.ecNode = cs[1];
            this.iconNode = cs[2];
            this.anchor = cs[3];
            this.textNode = cs[3].firstChild;
            if (a.qtip) {
                if (this.textNode.setAttributeNS) {
                    this.textNode.setAttributeNS("ext", "qtip", a.qtip);
                    if (a.qtipTitle) {
                        this.textNode.setAttributeNS("ext", "qtitle", a.qtipTitle);
                    }
                } else {
                    this.textNode.setAttribute("ext:qtip", a.qtip);
                    if (a.qtipTitle) {
                        this.textNode.setAttribute("ext:qtitle", a.qtipTitle);
                    }
                }
            }
            this.initEvents();
            if (!this.node.expanded) {
                this.updateExpandIcon();
            }
        } else {
            if (_25 === true) {
                _27.appendChild(this.wrap);
            }
        }
    };
});
