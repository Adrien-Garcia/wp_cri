'use strict';
/* global App, jsvar */

App.Utils = {

    device                      : {},

    queryString                 : false,

    animationUtilsAjaxSelector  : '.js-utils-animation-ajax',

    checkboxSelector            : '.js-utils-checkbox',
    checkboxStyleSelector       : '.js-utils-checkbox-style',
    checkboxCheckedClass        : 'select',

    $animationUtilsAjax         : null,

    $checkbox                   : null,
    $checkboxStyle              : null,

    init: function() {
        this.debug("Utils : init start");

        var self = this;

        this.queryString = this.getQueryString();

        this.$animationUtilsAjax        = $(this.animationUtilsAjaxSelector);

        this.$checkbox                  = $(this.checkboxSelector);
        this.$checkbox.each(function() {
            var $this = $(this);
            this.checkedStyle  = $this.closest(self.checkboxStyleSelector);
        });

        this.device.ie9 = /MSIE 9/i.test(navigator.userAgent);
        this.device.ie10 = /MSIE 10/i.test(navigator.userAgent);
        this.device.ie11 = /rv:11.0/i.test(navigator.userAgent);
        this.device.ie = this.device.ie9 || this.device.ie10 || this.device.ie11;

        this.device.ios = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);

        if(location.hash) {
            App.Utils.scrollTop(700, location.hash);
        }

        this.debug("Utils : init end");
        this.addListeners();

    },

    addListeners: function() {
        this.debug("Utils : addListeners start");
        var self = this;

        $(document).bind("ajaxSend", function(){
            self.openAjaxAnimation();
        }).bind("ajaxComplete", function(){
            self.closeAjaxAnimation();
        });

        this.$checkbox.on('change', function() {
            self.checkboxToggle($(this));
        });

        this.debug("Utils : addListeners end");

    },

    checkboxToggle: function( checkbox ) {
        if (checkbox[0].checked) {
            checkbox[0].checkedStyle.addClass(this.checkboxCheckedClass);
        } else {
            checkbox[0].checkedStyle.removeClass(this.checkboxCheckedClass);
        }
    },


    getQueryString: function() {
        var query_string = false;
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        var arr = [];
        if (vars.length > 0 && vars[0] !== "") {
            query_string = {};
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                // If first entry with this name create a key for it
                if (typeof query_string[pair[0]] === "undefined") {
                    query_string[pair[0]] = decodeURIComponent(pair[1]);
                    // If second entry with this name create an array and remove the previously created key
                } else if (typeof query_string[pair[0]] === "string") {
                    arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
                    query_string[pair[0]] = arr;
                    // If third or later entry with this name add to array
                } else {
                    query_string[pair[0]].push(decodeURIComponent(pair[1]));
                }
            }
        }
        return query_string;
    },

    getBodyClass: function() {
        return $('body')[0].className.split(/\s+/);
    },

    scrollTop: function(duration, hash) {
        var top = (hash !== undefined) ? $(hash).offset().top - ($("header.header").height() + 30) : 0;
        duration = (duration !== undefined) ? duration : 700;
        // hash = (hash !== undefined) ? hash : "";
        $('html, body').animate({
            scrollTop: top
        }, duration, function(){
            //window.location.hash = hash;
        });
    },

    openAjaxAnimation: function() {
        this.$animationUtilsAjax.addClass('loading');
    },

    closeAjaxAnimation: function() {
        this.$animationUtilsAjax.removeClass('loading');
    },

    debug: function(t) {
        App.debug(t);
    }
};