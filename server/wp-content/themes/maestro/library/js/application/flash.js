'use strict';

App.Flash = {

    flashBlockSelector          : '.js-flash-info',
    eventFlashOpenSelector      : '.js-flash-open',
    eventFlashCloseSelector     : '.js-flash-close',
    $flashBlock                 : null,
    $flashToggle                : null,

    init: function() {

        this.debug("Flash : init start");

        this.$flashBlock        = $(this.flashBlockSelector);
        this.$flashToggle       = $(this.eventFlashOpenSelector).add(this.eventFlashCloseSelector);

        this.addListeners();

        this.debug("Flash : init end");

    },

    /*
     * Listeners for the flash
     */

    addListeners: function() {
        var self = this;

        this.debug("Flash : addListeners start");

        this.$flashOpen.on("click", function(e) {
           self.eventFlashToggle($(this));
        });

        this.debug("Flash : addListeners end");
    },

    /*
     * Event for toggling on and off the flash 
     */

    eventFlashToggle: function() {
        this.$flashBlock.toggleClass("closed");
    },

    debug: function(t) {
        App.debug(t);
    }
};