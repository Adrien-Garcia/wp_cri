'use strict';

App.Utils = {

    device                      : {},

    queryString                 : false,

    animationUtilsAjaxSelector  : '.js-utils-animation-ajax',

    $animationUtilsAjax         : null,


    init: function() {
        this.debug("Utils : init start");

        var self = this;

        this.queryString = this.getQueryString();

        this.$animationUtilsAjax        = $(this.animationUtilsAjaxSelector);

        $(document).bind("ajaxSend", function(){
            self.openAjaxAnimation();
        }).bind("ajaxComplete", function(){
            self.closeAjaxAnimation();
        });

        this.device.ie9 = /MSIE 9/i.test(navigator.userAgent);
        this.device.ie10 = /MSIE 10/i.test(navigator.userAgent);
        this.device.ie11 = /rv:11.0/i.test(navigator.userAgent);
        this.device.ie = this.device.ie9 | this.device.ie10 | this.device.ie11;

        this.device.ios = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);

        this.debug("Utils : init end");

    },


    getQueryString: function() {
        var query_string = false;
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        var arr = [];
        if (vars.length > 0 && vars[0] != "") {
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
        var body_class = [];
        body_class = $('body')[0].className.split(/\s+/);
        return body_class;
    },

    scrollTop: function(duration, hash) {
        var top = (hash !== undefined) ? $(hash).offset().top - ($("header.header").height() + 30) : 0;
        duration = (duration !== undefined) ? duration : 700;
        hash = (hash !== undefined) ? hash : "";
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