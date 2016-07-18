'use strict';
/* global App, jsvar, wkf */

App.Cridonline = {
    prefix      : 'js-cridonline',
    linkSuffix  : '-link',
    linkMark    : null,
    linkValue   : '',
    canAccess   : 0,
    init: function() {
        var current_location = window.location;
        var self = this;

        this.debug("CridOnline : init start");

        this.linkMark = $('#' + this.prefix + this.linkSuffix);
        this.canAccess = parseInt(this.linkMark.data('js-cridonline-access'));
        this.linkValue =this.linkMark.attr('href');

        if (0 === this.canAccess) {
            // Default is if logged in but do not have the role CONST_CONNAISSANCE_ROLE
            if (App.Utils.queryString.openCridonOnline === "1"){
                // If coming from loggin with no role
                window.location = this.linkMark.data('js-redirect');
            } else if (!$('body').hasClass('is_notaire')){
                // If not logged in
                // Do not preserve current Query String : not needed as the purpose is to get to Crid'Online page
                this.linkValue = window.location + '?openLogin=1&messageLogin=PROTECTED_CONTENT&requestUrl=' + encodeURIComponent(current_location + '?openCridonOnline=1');
                this.linkMark.attr('href', this.linkValue);
            }
        } else {
            this.linkMark.on('click', function (e) {
                self.accessCridonline($(this));
                return false;
            });
            if (App.Utils.queryString.openCridonOnline === "1") {
                self.accessCridonline(this.linkMark);
            }
        }

        this.debug("CridOnline : init end");
    },
    accessCridonline: function(link) {
        jQuery.ajax({
            url: link.attr('href'),
            dataType: 'script',
            success: function() {
                if (typeof wkf != 'undefined') {
                    window.location = wkf.url;
                }
            },
            async: true
        });
    },
    debug: function(t) {
        App.debug(t);
    }
};
