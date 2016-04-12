'use strict';

App.Cridonline = {
    prefix      : 'js-cridonline',
    linkSuffix  : '-link',
    linkMark    : null,
    linkValue   : '',
    init: function() {
        this.debug("CridOnline : init start");

        this.linkMark = $('#' + this.prefix + this.linkSuffix);

        if (typeof wkf !== 'undefined') {
            this.linkValue = wkf.url;
            this.linkMark.attr('href', this.linkValue);
        } else {
            this.linkMark.attr('href', '?openLogin=1&messageLogin=PROTECTED_CONTENT');
        }

        this.debug("CridOnline : init end");
    },
    debug: function(t) {
        App.debug(t);
    }
};
