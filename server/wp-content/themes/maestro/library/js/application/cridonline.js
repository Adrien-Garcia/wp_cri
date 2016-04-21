'use strict';

App.Cridonline = {
    prefix      : 'js-cridonline',
    linkSuffix  : '-link',
    linkMark    : null,
    linkValue   : '',
    init: function() {
        var current_location = window.location;

        this.debug("CridOnline : init start");

        this.linkMark = $('#' + this.prefix + this.linkSuffix);

        if (typeof wkf === 'undefined') {
            // Do not preserve current Query String : not needed as the purpose is to get to Crid'Online page
            this.linkValue = window.location + '?openLogin=1&messageLogin=PROTECTED_CONTENT&requestUrl=';
            this.linkValue += encodeURIComponent(current_location + '?openCridonOnline=1');
        } else {
            this.linkValue = wkf.url;
            if (App.Utils.queryString['openCridonOnline'] == "1") {
                // if coming from login panel, redirect to Crid'Online page
                window.location = this.linkValue;
            }
        }
        this.linkMark.attr('href', this.linkValue);

        this.debug("CridOnline : init end");
    },
    debug: function(t) {
        App.debug(t);
    }
};
