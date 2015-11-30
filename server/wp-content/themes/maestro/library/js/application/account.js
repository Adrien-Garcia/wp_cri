'use strict';
App.Account = {
    defaultSelector                     :'.js-account',

    accountBlocksSelector               : '-blocs',

    accountDashboardSelector            : '-dashboard',
    accountQuestionSelector             : '-questions',
    accountProfilSelector               : '-profil',
    accountFacturationSelector          : '-facturation',

    ajaxSelector                        : '-ajax',

    accountSoldeDataSelector            : '#js-solde-data',
    accountSoldeSVGSelector             : '#solde-circle-path',

    eventAccountButtonSelector          : '-button',


    $accountBlocks                      : null,

    $accountDashboard                   : null,
    $accountQuestion                    : null,
    $accountProfil                      : null,
    $accountFacturation                 : null,
    $accountDashboardAjax               : null,
    $accountQuestionAjax                : null,
    $accountProfilAjax                  : null,
    $accountFacturationAjax             : null,

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

        var self = this;

        var d = this.defaultSelector;
        var b = this.eventAccountButtonSelector;
        var a = this.ajaxSelector;

        this.$accountBlocks             = $(d + this.accountBlocksSelector);

        this.$accountDashboardButton    = $(d + this.accountDashboardSelector + b);
        this.$accountQuestionButton     = $(d + this.accountQuestionSelector + b);
        this.$accountProfilButton       = $(d + this.accountProfilSelector + b);
        this.$accountFacturationButton  = $(d + this.accountFacturationSelector + b);

        this.$accountDashboard          = $(d + this.accountDashboardSelector);
        this.$accountQuestion           = $(d + this.accountQuestionSelector);
        this.$accountProfil             = $(d + this.accountProfilSelector);
        this.$accountFacturation        = $(d + this.accountFacturationSelector);

        this.$accountDashboardAjax      = this.$accountDashboard.find(d + a);
        this.$accountQuestionAjax       = this.$accountQuestion.find(d + a);
        this.$accountProfilAjax         = this.$accountProfil.find(d + a);
        this.$accountFacturationAjax    = this.$accountFacturation.find(d + a);

        this.$accountBlocks.each(function(i, e) {
            if ($(e).hasClass('active')) {
                var block = $(e).data('js-name');
                self['init' + block]();
            }
        });

        this.addListeners();

        this.debug("Account : init end");

    },

    initDashboard: function() {
        this.debug('Account : Init Dashboard');
        this.$accountSoldeSVG           = $(this.accountSoldeSVGSelector);
        this.$accountSoldeData          = $(this.accountSoldeDataSelector);
        this.reloadSolde();

    },

    initQuestions: function() {
        this.debug('Account : Init Questions');

    },

    initProfil: function() {
        this.debug('Account : Init Profil');

    },

    initFacturation: function() {
        this.debug('Account : Init Facturation');

    },

    /*
     * Listeners for the Account page events
     */

    addListeners: function() {
        var self = this;

        this.debug("Account : addListeners start");

        this.$accountDashboardButton.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountDashboardOpen($(this));
        });
        this.$accountQuestionButton.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountQuestionOpen($(this));
        });
        this.$accountProfilButton.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountProfilOpen($(this));
        });
        this.$accountFacturationButton.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountFacturationOpen($(this));
        });

        this.$accountBlocks.each(function(i, e) {
            if ($(e).hasClass('active')) {
                var block = $(e).data('js-name');
                self['addListeners' + block]();
            }
        });

        this.debug("Account : addListeners end");
    },

    /*
     * Listeners for the Account Dashboard
     */

    addListenersDashboard: function() {
        var self = this;

        this.debug("Account : addListenersDashboard start");



        this.debug("Account : addListenersDashboard end");
    },

    /*
     * Listeners for the Account Questions
     */

    addListenersQuestions: function() {
        var self = this;

        this.debug("Account : addListenersQuestions start");



        this.debug("Account : addListenersQuestions end");
    },

    /*
     * Listeners for the Account Profil
     */

    addListenersProfil: function() {
        var self = this;

        this.debug("Account : addListenersProfil start");



        this.debug("Account : addListenersProfil end");
    },

    /*
     * Listeners for the Account Facturation
     */

    addListenersFacturation: function() {
        var self = this;

        this.debug("Account : addListenersFacturation start");



        this.debug("Account : addListenersFacturation end");
    },


    /*
     * Event for Opening the dashboard (Ultimately AJAX)
     */
    eventAccountDashboardOpen: function() {
        var self = this;
        this.$accountBlocks.removeClass("active");
        this.$accountDashboard.addClass("active");
        $.ajax({
            url: this.$accountDashboard.data('js-ajax-src'),
            success: function(data)
            {
                self.$accountDashboardAjax.html(data);
                self.debug('Account Dashboard Loaded');
                self.initDashboard();
            }
        });
        App.Utils.scrollTop();
    },

    /*
     * Event for Opening the Question (Ultimately AJAX)
     */
    eventAccountQuestionOpen: function() {
        var self = this;
        this.$accountBlocks.removeClass("active");
        this.$accountQuestion.addClass("active");
        $.ajax({
            url: this.$accountQuestion.data('js-ajax-src'),
            success: function(data)
            {
                self.$accountQuestionAjax.html(data);
                self.debug('Account Question Loaded');
                self.initDashboard();
            }
        });
        App.Utils.scrollTop();

    },

    /*
     * Event for Opening the Profil (Ultimately AJAX)
     */
    eventAccountProfilOpen: function() {
        var self = this;
        this.$accountBlocks.removeClass("active");
        this.$accountProfil.addClass("active");
        $.ajax({
            url: this.$accountProfil.data('js-ajax-src'),
            success: function(data)
            {
                self.$accountProfilAjax.html(data);
                self.debug('Account Profil Loaded');
                self.initDashboard();
            }
        });
        App.Utils.scrollTop();

    },

    /*
     * Event for Opening the Facturation (Ultimately AJAX)
     */
    eventAccountFacturationOpen: function() {
        var self = this;
        this.$accountBlocks.removeClass("active");
        this.$accountFacturation.addClass("active");
        $.ajax({
            url: this.$accountFacturation.data('js-ajax-src'),
            success: function(data)
            {
                self.$accountFacturationAjax.html(data);
                self.debug('Account Facturation Loaded');
                self.initDashboard();
            }
        });
        App.Utils.scrollTop();

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