'use strict';
App.Account = {
    defaultSelector                     :'.js-account',

    accountBlocksSelector               : '-blocs',

    accountDashboardSelector            : '-dashboard',
    accountQuestionSelector             : '-questions',
    accountProfilSelector               : '-profil',
    accountFacturationSelector          : '-facturation',

    accountSoldeDataSelector            : '#js-solde-data',
    accountSoldeSVGSelector             : '#solde-circle-path',

    eventAccountButtonSelector          : '-button',


    $accountBlocks                      : null,

    $accountDashboard                   : null,
    $accountQuestion                    : null,
    $accountProfil                      : null,
    $accountFacturation                 : null,

    $accountDashboardButton             : null,
    $accountQuestionButton              : null,
    $accountProfilButton                : null,
    $accountFacturationButton           : null,

    $accountSoldeData                   : null,
    $accountSoldeSVG                    : null,

    solde                               : 0,
    soldeMax                            : 150,


    init: function() {

        this.debug("Account : init start");

        var d = this.defaultSelector;

        this.$accountBlocks             = $(d + this.accountBlocksSelector);

        this.$accountDashboard          = $(d + this.accountDashboardSelector);
        this.$accountQuestion           = $(d + this.accountQuestionSelector);
        this.$accountProfil             = $(d + this.accountProfilSelector);
        this.$accountFacturation        = $(d + this.accountFacturationSelector);

        this.$accountDashboardButton    = $(d + this.accountDashboardSelector + this.eventAccountButtonSelector);
        this.$accountQuestionButton     = $(d + this.accountQuestionSelector + this.eventAccountButtonSelector);
        this.$accountProfilButton       = $(d + this.accountProfilSelector + this.eventAccountButtonSelector);
        this.$accountFacturationButton  = $(d + this.accountFacturationSelector + this.eventAccountButtonSelector);

        this.$accountSoldeSVG           = $(this.accountSoldeSVGSelector);
        this.$accountSoldeData          = $(this.accountSoldeDataSelector);

        this.reloadSolde();

        this.addListeners();

        this.debug("Account : init end");

    },

    /*
     * Listeners for the Account page events
     */

    addListeners: function() {
        var self = this;

        this.debug("Account : addListeners start");

        this.$accountDashboardButton.on("click", function(e) {
            self.eventAccountDashboardOpen($(this));
        });
        this.$accountQuestionButton.on("click", function(e) {
            self.eventAccountQuestionOpen($(this));
        });
        this.$accountProfilButton.on("click", function(e) {
            self.eventAccountProfilOpen($(this));
        });
        this.$accountFacturationButton.on("click", function(e) {
            self.eventAccountFacturationOpen($(this));
        });

        this.debug("Account : addListeners end");
    },


    /*
     * Event for Opening the dashboard (Ultimately AJAX)
     */
    eventAccountDashboardOpen: function() {
        this.$accountBlocks.removeClass("active");
        this.$accountDashboard.addClass("active");
    },

    /*
     * Event for Opening the Question (Ultimately AJAX)
     */
    eventAccountQuestionOpen: function() {
        this.$accountBlocks.removeClass("active");
        this.$accountQuestion.addClass("active");
    },

    /*
     * Event for Opening the Profil (Ultimately AJAX)
     */
    eventAccountProfilOpen: function() {
        this.$accountBlocks.removeClass("active");
        this.$accountProfil.addClass("active");
    },

    /*
     * Event for Opening the Facturation (Ultimately AJAX)
     */
    eventAccountFacturationOpen: function() {
        this.$accountBlocks.removeClass("active");
        this.$accountFacturation.addClass("active");
    },

    reloadSolde: function() {
        this.$accountSoldeData          = $(this.accountSoldeDataSelector);
        this.$accountSoldeSVG           = $(this.accountSoldeSVGSelector);
        this.solde                      = this.$accountSoldeData.data("solde");
        this.soldeMax                   = this.$accountSoldeData.data("solde-max");

        var totalLength = this.$accountSoldeSVG.get(0).getTotalLength();
        var newLength = totalLength - ((totalLength / this.soldeMax) * this.solde);
        this.$accountSoldeSVG.css({'stroke-dashoffset': newLength, 'stroke-dasharray': totalLength});
    },


    debug: function(t) {
        App.debug(t);
    }
};