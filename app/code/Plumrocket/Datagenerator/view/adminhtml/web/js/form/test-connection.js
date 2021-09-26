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
    'jquery'
], function ($) {
    'use strict';

    return {
        messageContainer: $('#test_connection-note .message-container'),

        connect: function (url) {
            var self = this;
            $('body').trigger('processStart');

            $.ajax(url, {
                data: $('#upload_fieldset').serializeArray(),
                loader: true,
                success: function (response) {
                    self.messageContainer.css('color', 'green');
                },
                error: function (response) {
                    self.messageContainer.css('color', 'red');
                },
                complete : function (response) {
                    self.messageContainer.text(response.responseJSON.message);
                    $('body').trigger('processStop');
                }
            });
        }
    }
});

