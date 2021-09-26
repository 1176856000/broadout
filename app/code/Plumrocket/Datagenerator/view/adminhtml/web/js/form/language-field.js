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
],function ($) {
    'use strict';
    var countryLanguages, languageElem = $('#datagenerator_language');

    return {
        currentLanguage: 'es-ES',

        changeOptions: function (elem) {
            elem = $(elem);
            var country = elem.find('option:checked').val(), counter = 0;
            languageElem.find('option').remove();

            languageElem.on('change', this.afterChangeLanguage.bind(this));

            $.each(countryLanguages[country].value, function (code, language) {
                if (0 === counter) {
                    this.setCurrentLanguage(code);
                }
                languageElem.append($('<option></option>').attr('value', code).text(language));
                counter++
            }.bind(this));
        },

        initCountryLanguages: function (languages) {
            countryLanguages = languages;
        },

        afterChangeLanguage: function (event) {
            this.setCurrentLanguage($(event.target).val());
        },

        setCurrentLanguage: function (code) {
            if (code) {
                this.currentLanguage = code;
            }

            return this;
        },

        getCurrentLanguage: function () {
            return this.currentLanguage;
        }
    }
});
