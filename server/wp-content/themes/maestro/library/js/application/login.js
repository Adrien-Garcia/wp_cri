'use strict';

App.Login = {

    panelConnexionSelector              : '.js-panel-connexion',
    
    eventConnexionOpenSelector          : '.js-panel-connexion-open',
    eventConnexionCloseSelector         : '.js-panel-connexion-close',
    
    $panelConnexion                     : null,
    $panelConnexionOpen                 : null,
    $panelConnexionClose                : null,


    init: function() {
        this.debug("Login : init start");

        this.$panelConnexion            = $(this.panelConnexionSelector);
        this.$panelConnexionClose       = $(this.eventConnexionCloseSelector);
        this.$panelConnexionOpen        = $(this.eventConnexionOpenSelector);
        

        this.addListeners();
        this.debug("Login : init end");

    },

    /*
     * Listeners for the Login page events
     */

    addListeners: function() {
        var self = this;

        this.debug("Login : addListeners start");

        this.$panelConnexionOpen.on("click", function(e) {
           self.eventPanelConnexionToggle($(this));
        });

        this.$panelConnexionClose.on("click", function(e) {
           self.eventPanelConnexionToggle($(this));
        });
        
        this.debug("Login : addListeners end");
    },

    /*
     * Event for toggling on and off the flash 
     */

    eventPanelConnexionToggle: function() {
        this.$panelConnexion.toggleClass("open");
    },

   


    debug: function(t) {
        App.debug(t);
    }
};