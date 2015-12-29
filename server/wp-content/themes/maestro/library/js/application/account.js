'use strict';
App.Account = {
    defaultSelector                     :'.js-account',

    accountBlocksSelector               : '-blocs',
    accountMessageSelector              : '-message',

    accountDashboardSelector            : '-dashboard',
    accountQuestionSelector             : '-questions',
    accountProfilSelector               : '-profil',
    accountFacturationSelector          : '-facturation',

    accountQuestionMoreSelector         : '-more',
    accountProfilSubscriptionSelector   : '-subscription',
    accountProfilNewsletterSelector     : '-newsletter',
    accountFormSelector                 : '-form',

    accountEmailSelector                : '-email',
    accountStateSelector                : '-state',


    ajaxSelector                        : '-ajax',
    ajaxPaginationSelector              : '-pagination',
    paginationSelector                  : '.page-numbers',

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

    $accountQuestionMoreButton          : null,

    $accountProfilSubscription          : null,
    $accountProfilNewsletterForm        : null,
    $accountProfilNewsletterMessage     : null,
    $accountProfilNewsletterEmail       : null,
    $accountProfilNewsletterState       : null,

    $accountQuestionPagination          : null,

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
        if(App.Utils.device.ie9 || App.Utils.device.ie10 || App.Utils.device.ie11) {
            $("#solde-circle-path").attr('d',$('#solde-circle-path').attr('die'));
        }
        this.reloadSolde();
        this.addListenersDashboard();

    },

    initQuestions: function() {
        this.debug('Account : Init Questions');

        var d = this.defaultSelector;
        var a = this.ajaxSelector;
        var b = this.eventAccountButtonSelector;

        this.$accountQuestionPagination = $(d + a + this.ajaxPaginationSelector).find(this.paginationSelector);

        this.$accountQuestionMoreButton = $(d + this.accountQuestionSelector + this.accountQuestionMoreSelector + b);

        this.$accountQuestionMoreButton.each((function(i, el) {
            var h = $(el).siblings(d + this.accountQuestionSelector + this.accountQuestionMoreSelector).find('ul').first().outerHeight();
            $(el).siblings(d + this.accountQuestionSelector + this.accountQuestionMoreSelector).css('height', h);
        }).bind(this));

        if(Modernizr.inputtypes.date){

        }
        else{
            $( ".datepicker" ).datepicker();
        }

        this.addListenersQuestions();
    },

    initProfil: function() {
        this.debug('Account : Init Profil');

        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokennewsletter';
        nonce.id    = 'tokennewsletter';
        nonce.value = jsvar.newsletter_nonce;

        var d = this.defaultSelector;

        this.$accountProfilNewsletterForm = $(d + this.accountProfilSelector + this.accountProfilNewsletterSelector + this.accountFormSelector);
        this.$accountProfilNewsletterForm.append(nonce);

        this.$accountProfilNewsletterMessage = $(d + this.accountProfilSelector + this.accountProfilNewsletterSelector + this.accountMessageSelector);
        this.$accountProfilNewsletterEmail = $(d + this.accountProfilSelector + this.accountProfilNewsletterSelector + this.accountEmailSelector);
        this.$accountProfilNewsletterState = $(d + this.accountProfilSelector + this.accountProfilNewsletterSelector + this.accountStateSelector);


        this.$accountProfilSubscription = $(d + this.accountProfilSelector + this.accountProfilSubscriptionSelector);

        this.addListenersProfil();
    },

    initFacturation: function() {
        this.debug('Account : Init Facturation');
        this.addListenersFacturation();

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

        this.debug("Account : addListeners end");
    },

    /*
     * Listeners for the Account Dashboard
     */

    addListenersDashboard: function() {
        var self = this;

        this.debug("Account : addListenersDashboard");


    },

    /*
     * Listeners for the Account Questions
     */

    addListenersQuestions: function() {
        var self = this;

        this.debug("Account : addListenersQuestions");

        this.$accountQuestionPagination.on('click', function (e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountQuestionPagination($(this));
        });

        this.$accountQuestionMoreButton.on('click', function (e) {
            self.eventAccountQuestionMoreToggle($(this));
        });

    },

    /*
     * Listeners for the Account Profil
     */

    addListenersProfil: function() {
        var self = this;

        this.debug("Account : addListenersProfil");

        this.$accountProfilSubscription.on('change', function (e) {
            self.eventAccountProfilSubscriptionToggle($(this));
        });

        this.$accountProfilNewsletterForm.on('submit', function (e) {
            self.eventAccountProfilNewsletterSubmit($(this));
            return false;
        });
    },

    /*
     * Listeners for the Account Facturation
     */

    addListenersFacturation: function() {
        var self = this;

        this.debug("Account : addListenersFacturation");


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
                self.initQuestions();
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
                self.initProfil();
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
                self.initFacturation();
            }
        });
        App.Utils.scrollTop();

    },

    eventAccountQuestionPagination: function(link) {
        var self = this;
        var url = link.attr('href');


        this.$accountBlocks.removeClass("active");
        this.$accountQuestion.addClass("active");
        $.ajax({
            url: url,
            success: function(data)
            {
                self.$accountQuestionAjax.html(data);
                self.debug('Account Question Pagination Loaded');
                self.initQuestions();
                App.Utils.scrollTop(undefined, "#historique-questions");
            }
        });
    },

    eventAccountQuestionMoreToggle: function (button) {
        var d = this.defaultSelector;
        button.toggleClass('close');
        button.siblings(d + this.accountQuestionSelector + this.accountQuestionMoreSelector).toggleClass('open');
    },

    eventAccountProfilSubscriptionToggle: function (input) {
        var label = input.parents(
            this.defaultSelector +
            this.accountProfilSelector +
            this.accountProfilSubscriptionSelector +
            this.eventAccountButtonSelector
        ).first();
        if (label.hasClass('select')) {
            label.removeClass('select');
            label.addClass('unselect');
        } else if (label.hasClass('unselect')) {
            label.removeClass('unselect');
            label.addClass('select');
        } else {
            if (input[0].checked) {
                label.removeClass('unselect');
                label.addClass('select');
            } else {
                label.removeClass('select');
                label.addClass('unselect');
            }
        }
    },

    eventAccountProfilNewsletterSubmit: function () {
        this.$accountProfilNewsletterMessage.html('');
        var email = this.$accountProfilNewsletterEmail.val();
        if (email != '') {
            jQuery.ajax({
                type: 'POST',
                url: jsvar.ajaxurl,
                data: {
                    action: 'newsletter',
                    email: email,
                    token: $('#tokennewsletter').val(),
                    state: this.$accountProfilNewsletterState.val()
                },
                success: this.successNewsletterToggle.bind(this)
            });
        } else {
            this.$accountProfilNewsletterMessage.html(jsvar.newsletter_empty_error);
        }

        return false;
    },

    successNewsletterToggle: function (data) {
        data = JSON.parse(data);
        if(data == 'success')
        {
            this.eventAccountProfilOpen();
        }
        else
        {
            this.$accountProfilNewsletterMessage.html(jsvar.newsletter_email_error);
        }
        return false;
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