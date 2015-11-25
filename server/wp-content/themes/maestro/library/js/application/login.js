'use strict';

App.Login = {

    panelConnexionSelector              : '.js-panel-connexion',

    formConnexionSelector               : '.js-panel-connexion-connexion-form',
    formMdpSelector                     : '.js-panel-connexion-mdp-form',

    eventToConnexionSelector            : '.js-panel-connexion-to-connexion',
    eventToMdpSelector                  : '.js-panel-connexion-to-mdp',
    
    eventConnexionOpenSelector          : '.js-panel-connexion-open',
    eventConnexionCloseSelector         : '.js-panel-connexion-close',
    
    $panelConnexion                     : null,
    $panelConnexionOpen                 : null,
    $panelConnexionClose                : null,

    $formConnexion                      : null,

    $buttonToConnexion                  : null,
    $buttonToMdp                        : null,


    init: function() {
        this.debug("Login : init start");

        this.$panelConnexion            = $(this.panelConnexionSelector);
        this.$panelConnexionClose       = $(this.eventConnexionCloseSelector);
        this.$panelConnexionOpen        = $(this.eventConnexionOpenSelector);
        
        this.$formConnexion             = $(this.formConnexionSelector);
        this.$formMdp                   = $(this.formMdpSelector);

        this.$buttonToConnexion         = $(this.eventToConnexionSelector);
        this.$buttonToMdp               = $(this.eventToMdpSelector);

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

        this.$buttonToConnexion.on("click", function(e) {
            self.eventToConnexion($(this));
        });

        this.$buttonToMdp.on("click", function(e) {
            self.eventToMdp($(this));
        });
        
        this.debug("Login : addListeners end");
    },

    /*
     * Event for toggling on and off the flash 
     */

    eventPanelConnexionToggle: function() {
        this.$panelConnexion.toggleClass("open");
        if (this.$panelConnexion.hasClass("open")) {
            this.$formConnexion.addClass('active');
            this.$formMdp.removeClass('active');
        } else {
            this.$formConnexion.removeClass('active');
            this.$formMdp.removeClass('active');
        }
    },

    eventToConnexion : function() {
        this.$formConnexion.addClass("active");
        this.$formMdp.removeClass("active");
    },

   eventToMdp : function() {
        this.$formConnexion.removeClass("active");
        this.$formMdp.addClass("active");
    },

    debug: function(t) {
        App.debug(t);
    }
};