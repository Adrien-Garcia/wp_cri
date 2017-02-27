'use strict';
/* global App, jsvar */

App.Utils = {

    device: {},

    queryString: false,

    animationUtilsAjaxSelector: '.js-utils-animation-ajax',

    checkboxSelector: '.js-utils-checkbox',
    checkboxStyleSelector: '.js-utils-checkbox-style',
    checkboxCheckedClass: 'select',

    $animationUtilsAjax: null,

    $checkbox: null,
    $checkboxStyle: null,

    init: function () {
        var self = this;

        this.debug('Utils : init start');

        this.queryString = this.getQueryString();

        this.$animationUtilsAjax        = $(this.animationUtilsAjaxSelector);

        this.$checkbox                  = $(this.checkboxSelector);
        this.$checkbox.each(function () {
            var $this = $(this);
            this.checkedStyle  = $this.closest(self.checkboxStyleSelector);
        });

        this.device.ie9 = /MSIE 9/i.test(navigator.userAgent);
        this.device.ie10 = /MSIE 10/i.test(navigator.userAgent);
        this.device.ie11 = /rv:11.0/i.test(navigator.userAgent);
        this.device.ie = this.device.ie9 || this.device.ie10 || this.device.ie11;

        this.device.ios = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);

        if (location.hash) {
            App.Utils.scrollTop(700, location.hash);
        }

        this.debug('Utils : init end');
        this.addListeners();
    },

    addListeners: function () {
        var self = this;
        this.debug('Utils : addListeners start');

        $(document).bind('ajaxSend', function () {
            self.openAjaxAnimation();
        }).bind('ajaxComplete', function () {
            self.closeAjaxAnimation();
        });

        this.$checkbox.on('change', function () {
            self.checkboxToggle($(this));
        });

        this.debug('Utils : addListeners end');
    },

    checkboxToggle: function (checkbox) {
        if (checkbox[0].checked) {
            checkbox[0].checkedStyle.addClass(this.checkboxCheckedClass);
        } else {
            checkbox[0].checkedStyle.removeClass(this.checkboxCheckedClass);
        }
    },


    getQueryString: function () {
        var queryString = false;
        var query = window.location.search.substring(1);
        var vars = query.split('&');
        var arr = [];
        var i = 0;
        var pair = null;
        if (vars.length > 0 && vars[0] !== '') {
            queryString = {};
            for (i = 0; i < vars.length; i++) {
                pair = null;
                pair = vars[i].split('=');
                // If first entry with this name create a key for it
                if (typeof queryString[pair[0]] === 'undefined') {
                    queryString[pair[0]] = decodeURIComponent(pair[1]);
                    // If second entry with this name create an array and remove the previously created key
                } else if (typeof queryString[pair[0]] === 'string') {
                    arr = [queryString[pair[0]], decodeURIComponent(pair[1])];
                    queryString[pair[0]] = arr;
                    // If third or later entry with this name add to array
                } else {
                    queryString[pair[0]].push(decodeURIComponent(pair[1]));
                }
            }
        }
        return queryString;
    },

    getBodyClass: function () {
        return $('body')[0].className.split(/\s+/);
    },

    scrollTop: function (_duration, _hash, _offset, _element) {
        var top = (typeof _hash !== 'undefined') ? $(_hash).offset().top - ($('header.header').height() + 30) : 0;
        var duration = (typeof _duration !== 'undefined') ? _duration : 700;
        var offset = (typeof _offset !== 'undefined') ? _offset : 0;
        var element = (typeof _element !== 'undefined') ? _element : 'html, body';
        // hash = (hash !== undefined) ? hash : "";
        $(element).animate({
            scrollTop: top + offset,
        }, duration, function () {
            // window.location.hash = hash;
        });
    },

    openAjaxAnimation: function () {
        this.$animationUtilsAjax.addClass('loading');
    },

    closeAjaxAnimation: function () {
        this.$animationUtilsAjax.removeClass('loading');
    },

    multilineEllipsis: function (el) {
        var wordArray = [];
        if (typeof el.dataset.innerHTML !== 'undefined') {
            el.innerHTML = el.dataset.innerHTML;
        }
        el.dataset.innerHTML = el.innerHTML;

        wordArray = el.innerHTML.split(' ');

        while (el.scrollHeight > (el.offsetHeight + 1) && wordArray.length > 1) {
            wordArray.pop();
            el.innerHTML = wordArray.join(' ') + '&hellip;';
        }
    },

    unEllipsis: function (el) {
        if (el.dataset.innerHTML) {
            el.innerHTML = el.dataset.innerHTML;
        }
    },

    cumulativeOffset: function (_element) {
        var top = 0,
            left = 0,
            element = _element;
        do {
            top += element.offsetTop  || 0;
            left += element.offsetLeft || 0;
            element = element.offsetParent;
        } while (element);

        return {
            top: top,
            left: left,
        };
    },

    debug: function (t) {
        App.debug(t);
    },
};
