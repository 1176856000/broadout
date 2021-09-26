/**
 * Copyright © 2018. All rights reserved.
 */

define([
    'jquery',
    'jquery/ui',
    'owlcarousel'
], function($) {
    "use strict";
    $.widget('Zemez.carouselInit', {
        options: {
            nav: true,
            navText: false,
            dotsSpeed: false,
            items: 3,
            autoPlay: false,
            responsive: {
                0: {
                    items: 1
                },
                480: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 3
                }
            }
        },
        _create: function() {
            var $owl = this.element.owlCarousel(this.options);
            $('body').on('rtlEnabled', function() {
                $owl.data('owl.carousel').options.rtl = true;
                $owl.trigger('refresh.owl.carousel');
            });
        },
        _setOption: function(key, value) {
            this._super("_setOption", key, value);
        },
    });
    return $.Zemez.carouselInit;
});
