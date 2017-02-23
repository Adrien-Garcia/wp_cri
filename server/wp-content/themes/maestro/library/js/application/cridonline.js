'use strict';
/* global App, jsvar, wkf */

App.Cridonline = {
    linkSelector: '.js-cridonline-link',
    linkMark: null,
    linkValue: '',
    canAccess: 0,
    init: function () {
        var current_location = window.location;
        var self = this;

        this.debug('CridOnline : init start');

        this.linkMark = $(this.linkSelector);
        // values are the same on every links, only the first one is always loaded
        this.canAccess = parseInt(this.linkMark.first().data('js-cridonline-access'));
        this.linkValue = this.linkMark.first().attr('href');

        if (0 === this.canAccess) {
            // Default is if logged in but do not have the role CONST_CONNAISSANCE_ROLE
            if (App.Utils.queryString.openCridonOnline === '1') {
                // If coming from loggin with no role
                window.location = this.linkMark.first().data('js-redirect');
            } else if (!$('body').hasClass('is_notaire')) {
                // If not logged in
                // Do not preserve current Query String : not needed as the purpose is to get to Crid'Online page
                this.linkValue = window.location + '?openLogin=1&messageLogin=PROTECTED_CONTENT&requestUrl=' + encodeURIComponent(current_location + '?openCridonOnline=1');
                this.linkMark.each(function (index, element) {
                    $(element).attr('href', self.linkValue);
                });
            }
        } else {
            $('body').on('click', this.linkSelector, function (e) {
                self.accessCridonline($(this).attr('href'));
                return false;
            });
            if (App.Utils.queryString.openCridonOnline === '1') {
                self.accessCridonline(this.linkValue);
            }
        }

        this.debug('CridOnline : init end');
    },
    accessCridonline: function (url) {
        jQuery.ajax({
            url: url,
            dataType: 'script',
            success: function () {
                if (typeof wkf != 'undefined') {
                    window.location = wkf.url;
                }
            },
            async: true,
        });
    },
    debug: function (t) {
        App.debug(t);
    },
};
