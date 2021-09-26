define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_SalesRule/js/action/set-coupon-code',
        'Magento_SalesRule/js/view/payment/discount',
        'mage/translate'
    ],
    function ($, ko, Component, setCouponCodeAction, discount, $t) {
        "use strict";
        var fields = {
            'is_active': $t('is not active'),
            'from_date': $t('is not started yet'),
            'to_date': $t('is out of date'),
            'customer_group_id': $t('belongs to another group'),
            'website_id': $t('belongs to another website'),
            'usage_limit': $t('is usage limit exceeded'),
            'usage_per_customer': $t('is usage limit exceeded per customer')
        };
        var currentPage = '';
        return Component.extend({
            defaults: {
                template: 'Magenest_CouponCode/codeList'
            },

            rules: ko.observableArray(),

            timesUsedOfCustomer: ko.observable(),

            reason: ko.observable(),

            isApplied: ko.observable(discount.prototype.isApplied),

            enable: window.checkoutConfig.enable,

            is_checked: ko.observable(),

            initialize: function () {
                this._super();
		var array = window.checkoutConfig.rules;
		var arr1=[];
                for(var i = 0; i < array.length; i++){
                    if(array[i].is_display == "1"){
                        arr1.push(array[i]);
                    }
                }
                this.rules = ko.observableArray(arr1);
                //this.rules = ko.observableArray(window.checkoutConfig.rules);
                currentPage = this.current_page;
                return this;
            },

            displayFields: function (rule) {
                var showFieldsConfiguration = window.checkoutConfig.fieldDisplay.split(',');
                var hideFieldsConfiguration = [];
                var conditionFields = [
                    'coupon_code',
                    'description',
                    'uses_per_coupon',
                    'uses_per_customer',
                    'expiring',
                ];

                jQuery.grep(conditionFields, function (el) {
                    if (jQuery.inArray(el, showFieldsConfiguration) === -1)
                        hideFieldsConfiguration.push(el);
                });

                for (var i = 0; i < hideFieldsConfiguration.length; i++) {
                    this.configurationValidation(rule, hideFieldsConfiguration[i]);
                }
                return this;
            },

            configurationValidation: function (rule, condition) {
                switch (condition) {
                    case 'coupon_code':
                        $('.code').hide();
                        break;
                    case 'description':
                        $('.description').hide();
                        break;
                    case 'uses_per_coupon':
                        $('.usesPerCoupon').hide();
                        break;
                    case 'uses_per_customer':
                        $('.usesPerCustomer').hide();
                        break;
                    case 'expiring':
                        $('.toDate').hide();
                        break;
                    default:
                        break;
                }
            },

            showExpiring: function (rule) {
                var result = '';
                if (rule.to_date !== null && !this.validateCondition(rule, 'to_date')) {
                    var toDate = new Date(rule.to_date);
                    result = $t('Valid until: ') + toDate.toDateString();
                }
                return result;
            },

            dateStyling: function (rule) {
                var result = '';
                var nowData = new Date(this.getNowDate());
                if (rule.to_date != '') {
                    var expiring = new Date(rule.to_date);
                    var differenceInTime = expiring.getTime() - nowData.getTime();
                    var differenceInDays = differenceInTime / (1000 * 3600 * 24);
                    if (differenceInDays + 1 <= 7) {
                        result = 'expiring';
                    }
                }
                return result;
            },

            showDescription: function (rule) {
                var result = "";

                if (rule.description != '') {
                    result = rule.code + ' - ' + rule.description;
                } else result = rule.code;

                return $t(result);
            },

            showUsesPerCoupon: function (rule) {
                if (rule.usage_limit) {
                    var usage = parseInt(rule.usage_limit) - parseInt(rule.times_used_coupon);
                    return $t('Limited Offer: ') + usage.toString();
                }
            },

            showUsesPerCustomer: function (rule) {
                if (rule.usage_per_customer) {
                    if (!rule.times_used_customer) {
                        rule.times_used_customer = 0;
                    }
                    var usage = parseInt(rule.usage_per_customer) - parseInt(rule.times_used_customer);
                    return $t('Awarded: ') + usage.toString();
                }
            },

            useNow: function (rule) {
                if (currentPage === "checkout_cart_index"){
                    $('#coupon_code').val(rule.code);
                    $('#discount-coupon-form').submit();
                }else{
                    discount.prototype.couponCode(rule.code);
                    setCouponCodeAction(discount.prototype.couponCode(), discount.prototype.isApplied);
                }
            },

            checkConfigurationValidate: function (rule) {
                var self = this;
                var str = '';
                var flag = true;
                var conditionFields = [
                    'is_active',
                    'from_date',
                    'to_date',
                    'customer_group_id',
                    'website_id',
                    'usage_limit',
                    'usage_per_customer',
                ];
                for (var i = 0; i < conditionFields.length; i++) {
                    if (self.validateCondition(rule, conditionFields[i])) {
                        flag = false;
                        str += this.checkField(fields[conditionFields[i]]);
                    }
                }
                var result = $t('The coupon code ') + str.substring(0, str.length - 2).concat('.');
                this.reason = ko.observable(result);

                return flag;
            },

            checkField: function (value) {
                value += ', ';
                return value;
            },

            validateCondition: function (rule, field) {
                if (typeof rule[field] === undefined || rule[field] == null) {
                    return false;
                }
                var result = false;
                var customer_group_id = window.checkoutConfig.customerData['group_id'];
                var customer_id = window.checkoutConfig.customerData['id'];
                var website_id = window.checkoutConfig.website_id;
                if (customer_id == null) {
                    customer_group_id = '0';
                }
                var nowDate = this.getNowDate();

                switch (field) {
                    case 'is_active':
                        result = rule.is_active === '0';
                        break;
                    case 'usage_per_customer':
                        result = customer_id != null && (parseInt(rule.usage_per_customer) - parseInt(rule.times_used_customer)) === 0;
                        break;
                    case 'usage_limit':
                        result = (parseInt(rule.usage_limit) - parseInt(rule.times_used_coupon)) === 0;
                        break;
                    case 'website_id':
                        result = rule.website_id.indexOf(website_id) < 0;
                        break;
                    case 'customer_group_id':
                        result = rule.customer_group_id.indexOf(customer_group_id) < 0;
                        break;
                    case 'from_date':
                        result = rule.from_date > nowDate;
                        break;
                    case 'to_date':
                        result = rule.to_date < nowDate;
                        break;
                    default:
                        break;
                }

                return result;
            },

            getNowDate: function () {
                var d = new Date();
                var month = d.getMonth() + 1;
                var day = d.getDate();
                var nowDate = d.getFullYear() + '-' +
                    (month < 10 ? '0' : '') + month + '-' +
                    (day < 10 ? '0' : '') + day;
                return nowDate;
            },
        });

    }
);
