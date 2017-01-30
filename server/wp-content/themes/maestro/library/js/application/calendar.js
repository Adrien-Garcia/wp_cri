'use strict';
/* global App, jsvar, enquire */

App.Calendar = {

    ellispisSelector: '.js-calendar-ellipsis',

    $ellipsis: null,

    init: function () {
        var self = this;
        this.debug('init start');

        this.$ellipsis = $(this.ellispisSelector);

        enquire.register('screen and (min-width: 1240px)', function () {
            self.$ellipsis.each(function () {
                App.Utils.multilineEllipsis(this);
            });
        }).register('screen and (max-width: 1239px)', function () {
            self.$ellipsis.each(function () {
                App.Utils.unEllipsis(this);
            });
        });

        this.addListeners();

        this.debug('init end');
    },

    /*
     * Listeners for the Home page events
     */

    addListeners: function () {
        var self = this;

        this.debug('addListeners start');


        this.debug('addListeners end');
    },

    debug: function (t) {
        App.debug('Calendar : ' + t);
    },
};
