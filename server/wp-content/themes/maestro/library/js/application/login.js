'use strict';
App.Login = {

    panelConnexionSelector              : '.js-panel-connexion',

    formConnexionSelector               : '.js-panel-connexion-connexion-form',
    formMdpSelector                     : '.js-panel-connexion-mdp-form',

    eventToConnexionSelector            : '.js-panel-connexion-to-connexion',
    eventToMdpSelector                  : '.js-panel-connexion-to-mdp',

    eventConnexionOpenSelector          : '.js-panel-connexion-open',
    eventConnexionCloseSelector         : '.js-panel-connexion-close',

    eventConnexionErrorResetSelector    : '.js-panel-connexion-reset-error',

    blockConnexionErrorMessageSelector  : '.js-login-error-message-block',
    blockForgotErrorMessageSelector     : '.js-forgot-error-message-block',

    $panelConnexion                     : null,
    $panelConnexionOpen                 : null,
    $panelConnexionClose                : null,

    $formConnexion                      : null,

    $buttonToConnexion                  : null,
    $buttonToMdp                        : null,

    $elementsConnexionErrorReset        : null,

    $blockConnexionErrorMessage         : null,
    $blockForgotErrorMessage            : null,


    init: function() {
        this.debug("Login : init start");

        this.$panelConnexion            = $(this.panelConnexionSelector);
        this.$panelConnexionClose       = $(this.eventConnexionCloseSelector);
        this.$panelConnexionOpen        = $(this.eventConnexionOpenSelector);

        this.$formConnexion             = $(this.formConnexionSelector);
        this.$formMdp                   = $(this.formMdpSelector);

        this.$buttonToConnexion         = $(this.eventToConnexionSelector);
        this.$buttonToMdp               = $(this.eventToMdpSelector);

        this.$blockConnexionErrorMessage= $(this.blockConnexionErrorMessageSelector);
        this.$blockForgotErrorMessage   = $(this.blockForgotErrorMessageSelector);

        this.$elementsConnexionErrorReset=$(this.eventConnexionErrorResetSelector);

        this.addListeners();

        if (App.Utils.queryString['openLogin'] == "1") {
            this.eventPanelConnexionToggle();
        }

        if (App.Utils.queryString['messageLogin'] != undefined) {
            this.changeLoginErrorMessage(App.Utils.queryString['messageLogin']);
        }

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
        this.$elementsConnexionErrorReset.on('click focus', function(e) {
            self.eventErrorReset($(this));
        });


        this.debug("Login : addListeners end");
    },

    /*
     * Event for toggling on and off the flash
     */

    eventPanelConnexionToggle: function(button) {
        this.$panelConnexion.toggleClass("open");
        if (this.$panelConnexion.hasClass("open")) {
            this.$formConnexion.addClass('active');
            this.$formMdp.removeClass('active');
        } else {
            this.$formConnexion.removeClass('active');
            this.$formMdp.removeClass('active');
        }

        if (button && button.data('login-message')){
            this.changeLoginErrorMessage(button.data('login-message'));
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

    changeLoginErrorMessage : function(error) {
        var message = "";
        switch (error) {
            case "PROTECTED_CONTENT":
                message = "Ce contenu est réservé aux utilisateurs enregistrés, veuillez vous connecter.";
                break;
            case "ERROR_NOT_CONNECTED_QUESTION":
                message = "Veuillez vous connectez pour poser une question.";
                break;
            case "ERROR_NEWSLETTER_NOT_CONNECTED":
                message = "Veuillez vous connecter pour choisir vos abonnements.";
                break;
            default:
                message = error;
                break;
        }
        this.$blockConnexionErrorMessage.text(message);
    },

    eventErrorReset: function () {
        this.$blockConnexionErrorMessage.text("");
        this.$blockForgotErrorMessage.text("");
    },

    debug: function(t) {
        App.debug(t);
    },

};
