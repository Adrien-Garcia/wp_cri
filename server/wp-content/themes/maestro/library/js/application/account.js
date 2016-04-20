'use strict';
App.Account = {
    defaultSelector                     :'.js-account',

    accountBlocksSelector               : '-blocs',
    accountContentBlocksSelector        : '-content',
    accountMessageSelector              : '-message',

    accountDashboardSelector            : '-dashboard',
    accountQuestionSelector             : '-questions',
    accountProfilSelector               : '-profil',
    accountFacturationSelector          : '-facturation',
    accountCollaborateurSelector        : '-collaborateur',
    accountCridonlineSelector           : '-cridonline',
    accountValidationSelector           : '-validation',

    accountQuestionMoreSelector         : '-more',
    accountProfilSubscriptionSelector   : '-subscription',
    accountProfilNewsletterSelector     : '-newsletter',
    accountCollaborateurDeleteSelector  : '-delete',
    accountCollaborateurAddSelector     : '-add',
    accountFormSelector                 : '-form',

    accountEmailSelector                : '-email',
    accountStateSelector                : '-state',
    accountCGVSelector                  : '-cgv',
    accountCrpcenSelector               : '-crpcen',
    accountLevelSelector                : '-level',
    accountPriceSelector                : '-price',
    accountCheckboxSelector             : '-checkbox',
    accountStep1Selector                : '-step1',
    accountStep2Selector                : '-step2',
    accountIdSelector                   : '-id',
    accountFirstnameSelector            : '-firstname',
    accountLastnameSelector             : '-lastname',
    accountPhoneSelector                : '-phone',
    accountMobilephoneSelector          : '-mobilephone',
    accountFunctionSelector             : '-function',

    ajaxSelector                        : '-ajax',
    ajaxPaginationSelector              : '-pagination',
    paginationSelector                  : '.page-numbers',

    accountFilterFormSelector           : '-form',
    accountFilterSelector               : '-filter',
    accountFilterDateDuSelector         : '-du',
    accountFilterDateAuSelector         : '-au',
    accountFilterSelectMatiereSelector  : '-matiere',

    accountSoldeDataSelector            : '#js-solde-data',
    accountSoldeSVGSelector             : '#solde-circle-path',
    accountPopupCridonline              : '#layer-cridonline',
    accountCridonline                   : '#cridonline',
    accountPopupCollaborateurDelete     : '#layer-collaborateur-delete',
    accountPopupCollaborateurAdd        : '#layer-collaborateur-add',

    eventAccountButtonSelector          : '-button',


    $accountBlocks                      : null,
    $accountContentBlocks               : null,

    $accountDashboard                   : null,
    $accountQuestion                    : null,
    $accountProfil                      : null,
    $accountFacturation                 : null,
    $accountCollaborateur               : null,
    $accountCridonline                  : null,
    $accountDashboardAjax               : null,
    $accountQuestionAjax                : null,
    $accountProfilAjax                  : null,
    $accountFacturationAjax             : null,
    $accountCridonlineAjax              : null,
    $accountCollaborateurAjax           : null,

    $accountDashboardButton             : null,
    $accountQuestionButton              : null,
    $accountProfilButton                : null,
    $accountFacturationButton           : null,
    $accountCollaborateurButton         : null,
    $accountCridonlineButton            : null,

    $accountQuestionMoreButton          : null,

    $accountProfilSubscription          : null,
    $accountProfilNewsletterForm        : null,
    $accountProfilNewsletterMessage     : null,
    $accountProfilNewsletterEmail       : null,
    $accountProfilNewsletterState       : null,

    $accountCollaborateurDeleteId       : null,

    $formQuestionFilter                 : null,
    $dateQuestionFilterDu               : null,
    $dateQuestionFilterAu               : null,
    $selectQuestionFilterMatiere        : null,

    $accountQuestionPagination          : null,

    $popupCridonline                    : null,
    $popupCollaborateurDelete           : null,
    $popupCollaborateurAdd              : null,

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

        this.$accountBlocks              = $(d + this.accountBlocksSelector);
        this.$accountContentBlocks       = $(d + this.accountContentBlocksSelector);

        this.$accountDashboardButton     = $(d + this.accountDashboardSelector + b);
        this.$accountQuestionButton      = $(d + this.accountQuestionSelector + b);
        this.$accountProfilButton        = $(d + this.accountProfilSelector + b);
        this.$accountFacturationButton   = $(d + this.accountFacturationSelector + b);
        this.$accountCollaborateurButton = $(d + this.accountCollaborateurSelector + b);
        this.$accountCridonlineButton    = $(d + this.accountCridonlineSelector + b);

        this.$accountDashboard           = $(d + this.accountDashboardSelector);
        this.$accountQuestion            = $(d + this.accountQuestionSelector);
        this.$accountProfil              = $(d + this.accountProfilSelector);
        this.$accountFacturation         = $(d + this.accountFacturationSelector);
        this.$accountCollaborateur       = $(d + this.accountCollaborateurSelector);
        this.$accountCridonline          = $(d + this.accountCridonlineSelector);

        this.$accountDashboardAjax       = this.$accountDashboard.find(d + a);
        this.$accountQuestionAjax        = this.$accountQuestion.find(d + a);
        this.$accountProfilAjax          = this.$accountProfil.find(d + a);
        this.$accountFacturationAjax     = this.$accountFacturation.find(d + a);
        this.$accountCollaborateurAjax   = this.$accountCollaborateur.find(d + a);
        this.$accountCridonlineAjax      = this.$accountCridonline.find(d + a);

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
        var f = this.accountFilterSelector;

        this.$accountQuestionPagination = $(d + a + this.ajaxPaginationSelector).find(this.paginationSelector);

        this.$accountQuestionMoreButton = $(d + this.accountQuestionSelector + this.accountQuestionMoreSelector + b);

        this.$formQuestionFilter = $(d + this.accountFormSelector + f);
        this.$dateQuestionFilterAu = $(d + this.accountFilterDateAuSelector + f);
        this.$dateQuestionFilterDu = $(d + this.accountFilterDateDuSelector + f);
        this.$selectQuestionFilterMatiere = $(d + this.accountFilterSelectMatiereSelector + f);

        this.$accountQuestionMoreButton.each((function(i, el) {
            var h = $(el).siblings(d + this.accountQuestionSelector + this.accountQuestionMoreSelector).find('ul').first().outerHeight();
            $(el).siblings(d + this.accountQuestionSelector + this.accountQuestionMoreSelector).css('height', h);
        }).bind(this));

        if(Modernizr.inputtypes.date){

        }
        else{
            $.datepicker.setDefaults({
                dateFormat: "dd/mm/yy"
            });
            $( ".datepicker" ).datepicker();
            $( ".datepicker" ).datepicker("option", "dateFormat" , "dd/mm/yy");
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

    initCollaborateur: function() {
        this.debug('Account : Init Collaborateur');

        var d = this.defaultSelector;

        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokencollaborateur';
        nonce.id    = 'tokencollaborateur';
        nonce.value = jsvar.collaborateur_delete_nonce;

        this.$accountCollaborateurDeleteValidationForm    = $(d + this.accountCollaborateurSelector + this.accountCollaborateurDeleteSelector + this.accountValidationSelector + this.accountFormSelector);
        this.$accountCollaborateurDeleteValidationForm.append(nonce);
        this.$accountCollaborateurDeleteValidationId      = $(d + this.accountCollaborateurSelector + this.accountCollaborateurDeleteSelector + this.accountValidationSelector + this.accountIdSelector);
        this.$accountCollaborateurDeleteValidationMessage = $(d + this.accountCollaborateurSelector + this.accountCollaborateurDeleteSelector + this.accountValidationSelector + this.accountMessageSelector);

        this.$accountCollaborateurDeleteForm = $(d + this.accountCollaborateurSelector + this.accountCollaborateurDeleteSelector + this.accountFormSelector);
        this.$accountCollaborateurDeleteId   = $(d + this.accountCollaborateurSelector + this.accountCollaborateurDeleteSelector + this.accountIdSelector);

        this.$accountCollaborateurAddForm        = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountFormSelector);
        this.$accountCollaborateurAddFirstname   = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountFirstnameSelector);
        this.$accountCollaborateurAddLastname    = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountLastnameSelector);
        this.$accountCollaborateurAddPhone       = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountPhoneSelector);
        this.$accountCollaborateurAddMobilephone = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountMobilephoneSelector);
        this.$accountCollaborateurAddEmail       = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountEmailSelector);
        this.$accountCollaborateurAddFunction    = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountFunctionSelector);
        this.$accountCollaborateurAddMessage     = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.accountMessageSelector);

        this.$popupCollaborateurDelete       = $(this.accountPopupCollaborateurDelete);
        this.$popupCollaborateurAdd       = $(this.accountPopupCollaborateurAdd);

        this.popupCollaborateurDeleteInit();
        this.popupCollaborateurAddInit();

        this.$accountCollaborateurAddButton = $(d + this.accountCollaborateurSelector + this.accountCollaborateurAddSelector + this.eventAccountButtonSelector);

        this.addListenersCollaborateur();
    },

    popupCollaborateurDeleteInit: function() {
        var self = this;
        this.$popupCollaborateurDelete.popup({
            transition: 'all 0.3s',
            scrolllock: true,
            opacity: 0.8,
            color: '#324968',
            offsettop: 10,
            vertical: top
        });
    },

    popupCollaborateurAddInit: function() {
        var self = this;
        this.$popupCollaborateurAdd.popup({
            transition: 'all 0.3s',
            scrolllock: true,
            opacity: 0.8,
            color: '#324968',
            offsettop: 10,
            vertical: top
        });
    },

    initCridonline: function() {
        this.debug('Account : Init Cridonline');

        var d = this.defaultSelector;

        this.$accountCridonlineForm    = $(d + this.accountCridonlineSelector + this.accountFormSelector);

        this.$accountCridonlineMessage = $(d + this.accountCridonlineSelector + this.accountMessageSelector);
        this.$accountCridonlineCrpcen  = $(d + this.accountCridonlineSelector + this.accountCrpcenSelector);
        this.$accountCridonlineLevel   = $(d + this.accountCridonlineSelector + this.accountLevelSelector);
        this.$accountCridonlinePrice   = $(d + this.accountCridonlineSelector + this.accountPriceSelector);

        this.$cridonline               = $(this.accountCridonline);

        this.addListenersCridonline();

    },

    initCridonlineValidation: function() {
        this.debug('Account : Init Cridonline Validation');

        var d = this.defaultSelector;

        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokencridonline';
        nonce.id    = 'tokencridonline';
        nonce.value = jsvar.cridonline_nonce;

        this.$accountCridonlineValidationForm    = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountFormSelector);
        this.$accountCridonlineValidationForm.append(nonce);

        this.$accountCridonlineValidationMessage = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountMessageSelector);
        this.$accountCridonlineValidationCGV     = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountCGVSelector);
        this.$accountCridonlineValidationCrpcen  = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountCrpcenSelector);
        this.$accountCridonlineValidationLevel   = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountLevelSelector);
        this.$accountCridonlineValidationPrice   = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountPriceSelector);
        this.$accountCridonlineValidationStep1   = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountStep1Selector);
        this.$accountCridonlineValidationStep2   = $(d + this.accountCridonlineSelector + this.accountValidationSelector + this.accountStep2Selector);

        this.$popupCridonline                    = $(this.accountPopupCridonline);

        this.popupCridonlineInit();

        this.addListenersCridonlineValidation();
    },

    popupCridonlineInit: function() {
        var self = this;
        this.$popupCridonline.popup({
            transition: 'all 0.3s',
            scrolllock: true,
            opacity: 0.8,
            color: '#324968',
            offsettop: 10,
            vertical: top
        });
        this.$accountCridonlineValidationStep1.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.$accountCridonlineValidationStep1.toggle();
            self.$accountCridonlineValidationStep2.toggle();
        });
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
        this.$accountCollaborateurButton.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountCollaborateurOpen($(this));
        });
        this.$accountCridonlineButton.on("click", function(e) {
            e.returnValue = false;
            e.preventDefault();
            self.eventAccountCridonlineOpen($(this));
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

        this.$selectQuestionFilterMatiere
            .add(this.$dateQuestionFilterAu)
            .add(this.$dateQuestionFilterDu)
            .on('change', function (e) {
            self.eventQuestionFilter($(this));
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
     * Listeners for the Account Cridonline
     */

    addListenersCridonline: function() {
        var self = this;

        this.debug("Account : addListenersCridonline");

        this.$accountCridonlineForm.on('submit', function (e) {
            self.eventAccountCridonlineSubmit($(this));
            return false;
        });
    },

    /*
     * Listeners for the Account Cridonline Validation (étape 2)
     */

    addListenersCridonlineValidation: function() {
        var self = this;

        this.debug("Account : addListenersCridonlineValidation");

        this.$accountCridonlineValidationCGV.on('change', function (e) {
            self.eventAccountCridonlineValidationCGV($(this));
        });

        this.$accountCridonlineValidationForm.on('submit', function (e) {
            self.eventAccountCridonlineValidationSubmit($(this));
            return false;
        });
    },

    /*
     * Listeners for the Account Collaborateur
     */

    addListenersCollaborateur: function() {
        var self = this;

        this.debug("Account : addListenersCollaborateur");

        this.$accountCollaborateurDeleteForm.on('submit', function (e) {
            self.eventAccountCollaborateurDeleteSubmit($(this));
            return false;
        });

        this.$accountCollaborateurDeleteValidationForm.on('submit', function (e) {
            self.eventAccountCollaborateurDeleteValidationSubmit($(this));
            return false;
        });

        this.$accountCollaborateurAddButton.on('click', function (e) {
            self.$popupCollaborateurAdd.popup('show');
        });

        this.$accountCollaborateurAddForm.on('submit', function (e) {
            self.eventAccountCollaborateurAddSubmit($(this));
            return false;
        });

    },

    /*
     * Event for Opening the dashboard (Ultimately AJAX)
     */
    eventAccountDashboardOpen: function(link) {
        var self = this;
        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountDashboard.addClass("active");
        $.ajax({
            url: link.data('js-ajax-src'),
            success: function(data)
            {
                $('#'+targetid).html(data);
                // $('#'+targetid).html(data);
                self.debug('Account Dashboard Loaded');
                self.initDashboard();
            }
        });
        App.Utils.scrollTop();
    },

    /*
     * Event for Opening the Question (Ultimately AJAX)
     */
    eventAccountQuestionOpen: function(link) {
        var self = this;
        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountQuestion.addClass("active");
        $.ajax({
            url: link.data('js-ajax-src'),
            success: function(data)
            {
                $('#'+targetid).html(data);
                // self.$accountQuestionAjax.html(data);
                self.debug('Account Question Loaded');
                self.initQuestions();
            }
        });
        App.Utils.scrollTop();

    },

    /*
     * Event for Opening the Profil (Ultimately AJAX)
     */
    eventAccountProfilOpen: function(link) {
        var self = this;
        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountProfil.addClass("active");
        $.ajax({
            url: link.data('js-ajax-src'),
            success: function(data)
            {
                $('#'+targetid).html(data);
                // self.$accountProfilAjax.html(data);
                self.debug('Account Profil Loaded');
                self.initProfil();
            }
        });
        App.Utils.scrollTop();

    },

    /*
     * Event for Opening the Facturation (Ultimately AJAX)
     */
    eventAccountFacturationOpen: function(link) {
        var self = this;
        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountFacturation.addClass("active");
        $.ajax({
            url: link.data('js-ajax-src'),
            success: function(data)
            {
                $('#'+targetid).html(data);
                // self.$accountFacturationAjax.html(data);
                self.debug('Account Facturation Loaded');
                self.initFacturation();
            }
        });
        App.Utils.scrollTop();

    },

    /*
     * Event for Opening the Collaborateur (AJAX)
     */
    eventAccountCollaborateurOpen: function(link) {
        var self = this;
        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountCollaborateur.addClass("active");
        $.ajax({
            url: link.data('js-ajax-src'),
            success: function(data)
            {
                $('#'+targetid).html(data);
                // self.$accountCollaborateurAjax.html(data);
                self.debug('Account Collaborateur Loaded');
                self.initCollaborateur();
            }
        });
        App.Utils.scrollTop();

    },

    /*
     * Event for Opening the Cridonline (Ultimately AJAX)
     */
    eventAccountCridonlineOpen: function(link) {
        var self = this;
        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountCridonline.addClass("active");
        $.ajax({
            url: link.data('js-ajax-src'),
            success: function(data)
            {
                $('#'+targetid).html(data);
                // self.$accountCridonlineAjax.html(data);
                self.debug('Account Cridonline Loaded');
                self.initCridonline();
            }
        });
        App.Utils.scrollTop();

    },

    eventAccountQuestionPagination: function(link) {
        var self = this;
        var url = link.attr('href');


        var targetid = link.data('js-target-id');
        this.$accountBlocks.removeClass("active");
        this.$accountContentBlocks.removeClass("active");
        this.$accountQuestion.addClass("active");
        $.ajax({
            url: url,
            success: function(data)
            {
                $('#'+targetid).html(data);
                // self.$accountQuestionAjax.html(data);
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

    eventAccountProfilNewsletterSubmit: function (form) {
        this.$accountProfilNewsletterMessage.html('');
        var email = this.$accountProfilNewsletterEmail.val();
        if (email != '') {
            jQuery.ajax({
                type: 'POST',
                url: form.data('js-ajax-newsletter-url'),
                data: {
                    token: $('#tokennewsletter').val(),
                    email: email,
                    state: this.$accountProfilNewsletterState.val()
                },
                success: this.successNewsletterToggle.bind(this)
            });
        } else {
            this.$accountProfilNewsletterMessage.html(jsvar.newsletter_empty_error);
        }

        return false;
    },

    eventAccountCridonlineSubmit: function (form) {
        jQuery.ajax({
            type: 'GET',
            url: form.data('js-ajax-validation-url'),
            data: {
                crpcen: form.find(this.$accountCridonlineCrpcen).val(),
                level: form.find(this.$accountCridonlineLevel).val(),
                price: form.find(this.$accountCridonlinePrice).val()
            },
            success: this.successCridonline.bind(this)
        });
        return false;
    },

    successCridonline: function (html) {
        this.$cridonline.html(html);
        this.initCridonlineValidation();
        App.Utils.scrollTop(undefined, "#cridonline-validation-popup");
    },

    eventAccountCollaborateurDeleteSubmit: function (form) {

        this.$accountCollaborateurDeleteValidationId.value = form.find(this.$accountCollaborateurDeleteId).val();

        this.$popupCollaborateurDelete.popup('show');
    },

    eventAccountCollaborateurDeleteValidationSubmit: function(form){
        this.$accountCollaborateurDeleteValidationMessage.html('');
        jQuery.ajax({
            type: 'POST',
            url: form.data('js-ajax-delete-validation-url') + this.$accountCollaborateurDeleteValidationId.value,
            data: {
                token: $('#tokencollaborateur').val()
            },
            success: this.successCollaborateurDelete.bind(this)
        });
        return false;
    },

    successCollaborateurDelete: function (data) {
        data = JSON.parse(data);

        if(data == 'success')
        {
            this.$accountCollaborateurDeleteValidationMessage.html(jsvar.collaborateur_delete_success);
        }
        else
        {
            this.$accountCollaborateurDeleteValidationMessage.html(jsvar.collaborateur_delete_fail);
        }
        return false;
    },

    eventAccountCollaborateurAddSubmit: function(form) {
        this.$accountCollaborateurAddMessage.html('');
        jQuery.ajax({
            type: 'POST',
            url: form.data('js-ajax-add-url'),
            data: {
                token: $('#tokencollaborateur').val(),
                collaborator_first_name: form.find(this.$accountCollaborateurAddFirstname).val(),
                collaborator_last_name: form.find(this.$accountCollaborateurAddLastname).val(),
                collaborator_tel: form.find(this.$accountCollaborateurAddPhone).val(),
                collaborator_tel_portable: form.find(this.$accountCollaborateurAddMobilephone).val(),
                collaborator_email: form.find(this.$accountCollaborateurAddEmail).val(),
                collaborator_function: form.find(this.$accountCollaborateurAddFunction).val()
            },
            success: this.successCollaborateurAdd.bind(this)
        });
        return false;
    },

    successCollaborateurAdd: function (data) {
        data = JSON.parse(data);
        if(data == 'success')
        {
            this.$accountCollaborateurAddMessage.html(jsvar.cridonline_add_success);
        }
        else
        {
            this.$accountCollaborateurAddMessage.html(jsvar.cridonline_add_error);
        }
        return false;
    },

    eventAccountCridonlineValidationSubmit: function (form) {
        this.$accountCridonlineValidationMessage.html('');
        jQuery.ajax({
            type: 'POST',
            url: form.data('js-ajax-souscription-url'),
            data: {
                token: $('#tokencridonline').val(),
                CGV: form.find(this.$accountCridonlineValidationCGV)[0].checked,
                crpcen: form.find(this.$accountCridonlineValidationCrpcen).val(),
                level: form.find(this.$accountCridonlineValidationLevel).val(),
                price: form.find(this.$accountCridonlineValidationPrice).val()
            },
            success: this.successCridonlineValidation.bind(this)
        });
        return false;
    },

    successCridonlineValidation: function (data) {
         data = JSON.parse(data);

         if(data == 'success')
         {
            this.$popupCridonline.popup('show');
         }
         else
         {
            this.$accountCridonlineValidationMessage.html(jsvar.cridonline_CGV_error);
         }
         return false;
    },

    eventAccountCridonlineValidationCGV: function (input) {
        var label = input.parents(
            this.defaultSelector +
            this.accountCridonlineSelector +
            this.accountValidationSelector +
            this.accountCheckboxSelector
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

    eventQuestionFilter: function () {
        var formdata = new FormData();
        this.$formQuestionFilter.submit();
        return;
        /*formdata.append("action", this.$formQuestionFilter[0].action);
        formdata.append("m", this.$selectQuestionFilterMatiere.first().val() );
        formdata.append("d1", this.$dateQuestionFilterDu.first().val() );
        formdata.append("d2", this.$dateQuestionFilterAu.first().val() );

        jQuery.ajax({
            type: 'POST',
            url: this.$formQuestionFilter[0].action,
            data: formdata,
            processData: false,
            contentType: false,
            success: this.successQuestionFilter.bind(this)
        });*/
    },

    successQuestionFilter: function (data) {

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
