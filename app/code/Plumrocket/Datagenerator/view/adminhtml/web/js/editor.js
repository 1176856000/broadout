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
    "Plumrocket_Datagenerator/js/lib/codemirror",
    "Plumrocket_Datagenerator/js/mode/xml/xml",
    "Plumrocket_Datagenerator/js/lib/util/closetag",
    "Plumrocket_Datagenerator/js/jquery-autocomplete-inner-master"
], function($, codemirror, xml,  closetag, autocomplete ){
    "use strict";

    return {

    	options : {
    		headerCodeId : 'code_header',
    		itemCodeId : 'code_item',
    		footerCodeId : 'code_footer'
    	},


        init : function() {
            window.headerEditor = CodeMirror.fromTextArea(document.getElementById(this.options.headerCodeId), {
				mode: {name: "xml", alignCDATA: true},
				lineNumbers: true,
				extraKeys: {
					"'>'": function(cm) { cm.closeTag(cm, '>'); },
					"'/'": function(cm) { cm.closeTag(cm, '/'); }
				},
				onChange: this.updateHeader(this)
			});

			window.itemEditor = CodeMirror.fromTextArea(document.getElementById(this.options.itemCodeId), {
				mode: {name: "xml", alignCDATA: true},
				lineNumbers: true,
				extraKeys: {
					"'>'": function(cm) { cm.closeTag(cm, '>'); },
					"'/'": function(cm) { cm.closeTag(cm, '/'); }
				},
				onChange: this.updateItem(this)
			});

			window.footerEditor = CodeMirror.fromTextArea(document.getElementById(this.options.footerCodeId), {
				mode: {name: "xml", alignCDATA: true},
				lineNumbers: true,
				extraKeys: {
					"'>'": function(cm) { cm.closeTag(cm, '>'); },
					"'/'": function(cm) { cm.closeTag(cm, '/'); }
				},
				onChange: this.updateFooter(this)
			});

            this.updateWidth();
            var updateWidth = this.updateWidth;
			$(window).resize(updateWidth);
            autocomplete.init();
        },

        update: function(data) {

        	if (window.headerEditor) {

                if (data[this.options.headerCodeId] === null) {
                    data[this.options.headerCodeId] = '';
                }

                headerEditor.setValue(data[this.options.headerCodeId]);
            }

            if (window.itemEditor) {
                if (data[this.options.itemCodeId] === null) {
                    data[this.options.itemCodeId] = '';
                }

                itemEditor.setValue(data[this.options.itemCodeId]);
            }

            if (window.footerEditor) {
                if (data[this.options.footerCodeId] === null) {
                    data[this.options.footerCodeId] = '';
                }

                footerEditor.setValue(data[this.options.footerCodeId]);
            }
        },

        refresh: function() {

        	window.headerEditor.refresh();
			window.itemEditor.refresh();
			window.footerEditor.refresh();            
        },

        updateWidth: function() {
            var containerWidth =  jQuery(window).width();
            var newwidth = containerWidth - 800;
            if (containerWidth > 1024) {
                jQuery('#prdatagenerator_tabs_editor_section_content tr').each(function() {
                    jQuery('td:first', $(this)).css('width', newwidth + 'px');
                });
                jQuery('.CodeMirror').css('width', newwidth + 'px');
            }
        },

        updateHeader: function(self) {
        	if (window.headerEditor) {
        		document.getElementById(self.options.headerCodeId).value = window.headerEditor.getValue();
        	}
        },

        updateItem: function(self) {
        	if (window.itemEditor) {
        		document.getElementById(self.options.itemCodeId).value = window.itemEditor.getValue();
        	}
        },

        updateFooter: function(self) {
        	if (window.footerEditor) {
        		document.getElementById(self.options.footerCodeId).value = window.footerEditor.getValue();
        	}
        }
    }
    
});