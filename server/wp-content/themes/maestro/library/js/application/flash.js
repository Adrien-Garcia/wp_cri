'use strict';

App.Flash = {

    flashBlockSelector          : '.js-flash-info',
    eventFlashToggleSelector    : '.js-flash-toggle',
    $flashBlock                 : null,
    $flashToggle                : null,

    init: function() {

        this.debug("Flash : init start");

        this.$flashBlock        = $(this.flashBlockSelector);
        this.$flashToggle       = $(this.eventFlashToggleSelector);

        this.debug("Flash : init end");

    },

    /*
     * Listeners for the flash
     */

    addListeners: function() {
        
        this.debug("Flash : addListeners start");

        this.$flashToggle.on("click", function(e) {
           this.eventFlashToggle($(this));
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