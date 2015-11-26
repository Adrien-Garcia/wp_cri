App.Question = {

    buttonQuestionOpenSelector          : '.js-question-open',

    selectQuestionMatiereSelector       : '.js-question-select-matiere',
    selectQuestionCompetenceSelector    : '.js-question-select-competence',

    zoneQuestionSupportSelector         : '.js-question-support-zone',
    radioQuestionSupportSelector        : '.js-question-support-radio',
    fileQuestionSelector                : '.js-question-file',
    fileQuestionResetSelector           : '.js-file-reset',
    fileQuestionNameSelector            : '.js-file-name',

    selectQuestionCompetenceName        : 'question_competence',

    tabQuestionConsultationSelector     : '.js-question-tab-consultation',
    tabQuestionMaQuestionSelector       : '.js-question-tab-ma-question',
    buttonQuestionConsultationSelector  : '.js-question-button-consultation',
    buttonQuestionMaQuestionSelector    : '.js-question-button-ma-question',

    owlCarouselSelector                 : "#owl-support",
    popupOverlaySelector                : "#layer-posez-question",

    selectedClass                       : 'selected',

    $buttonQuestionOpen                 : null,
    $selectQuestionMatiere              : null,
    $selectQuestionCompetence           : null,
    $selectQuestionCompetenceArray      : [],
    $zoneQuestionSupport                : null,
    $radioQuestionSupport               : null,
    $fileQuestion                       : null,
    $fileQuestionReset                  : null,
    $fileQuestionName                   : null,

    $tabQuestionConsultation            : null,
    $tabQuestionMaQuestion              : null,
    $buttonQuestionConsultation         : null,
    $buttonQuestionMaQuestion           : null,

    $owlCarousel                        : null,
    $popupOverlay                       : null,


    init: function() {
        this.debug("Question : init start");

        var self = this;

        this.$buttonQuestionOpen                    = $(this.buttonQuestionOpenSelector);

        this.$selectQuestionMatiere                 = $(this.selectQuestionMatiereSelector);
        this.$selectQuestionCompetence              = $(this.selectQuestionCompetenceSelector);

        this.$selectQuestionCompetence.each(function(i) {
            self.$selectQuestionCompetenceArray[$(this).data('matiere-id')] = $(this);
        });

        this.$tabQuestionConsultation               = $(this.tabQuestionConsultationSelector);
        this.$tabQuestionMaQuestion                 = $(this.tabQuestionMaQuestionSelector);
        this.$buttonQuestionConsultation            = $(this.buttonQuestionConsultationSelector);
        this.$buttonQuestionMaQuestion              = $(this.buttonQuestionMaQuestionSelector);

        this.$zoneQuestionSupport                   = $(this.zoneQuestionSupportSelector);
        this.$radioQuestionSupport                  = $(this.radioQuestionSupportSelector);
        this.$fileQuestion                          = $(this.fileQuestionSelector);
        this.$fileQuestionReset                     = $(this.fileQuestionResetSelector);
        this.$fileQuestionName                      = $(this.fileQuestionNameSelector);

        this.$owlCarousel                           = $(this.owlCarouselSelector);
        this.$popupOverlay                          = $(this.popupOverlaySelector);

        this.addListeners();

        var bClass = App.Utils.getBodyClass();

        if (bClass.indexOf("is_notaire") !== -1) {
            this.popupOverlayInit();
        }


        this.debug("Question : init end");

    },

    popupOverlayInit: function() {
        /* POPUP OVERLAY*/

        this.$popupOverlay.popup({
            transition: 'all 0.3s',
            scrolllock: true,
            opacity: 0.99,
            color: '#324968',
            offsettop: 10,
            vertical: top,
            onopen: function() {
                App.Question.owlCarouselInit();
            }

        });
    },

    owlCarouselInit: function() {

        this.$owlCarousel.owlCarousel({

            pagination: false,
            dots: false,
            navText: false,
            itemClass: 'owl-item ' + this.zoneQuestionSupportSelector.substr(1),
            onInitialized: this.addListenersAfterOwl.bind(this),
            responsive:{
                0 : {
                    items:1,
                    dots:true,
                    nav:true,

                },
                // breakpoint from 480 up
                768 : {
                    items:3,
                    dots:false,
                },
                // breakpoint from 768 up
                1200 : {
                    items:3,
                    dots:false,
                }
            }

        });
    },


    /*
     * Listeners for the Question page events
     */
    addListeners: function() {
        var self = this;
        var bClass = App.Utils.getBodyClass();

        this.debug("Question : addListeners start");


        this.$selectQuestionMatiere.on("change", function(e) {
            self.eventSelectQuestionMatiereChange($(this));
        });

        this.$buttonQuestionConsultation.on('click', function(e) {
            self.openTabQuestionConsultation($(this));
        });

        this.$buttonQuestionMaQuestion.on('click', function(e) {
            self.openTabQuestionMaQuestion($(this));
        });

        this.$fileQuestion.on('change', function(e) {
            self.eventFileChange($(this));
        });

        this.$fileQuestionReset.on('click', function(e) {
            self.eventFileReset($(this),e);
        });

        if ( bClass.indexOf("is_notaire") === -1) {
            this.$buttonQuestionOpen.on('click', function(e) {
                App.Login.eventPanelConnexionToggle();
                App.Login.changeLoginErrorMessage("ERROR_NOT_CONNECTED_QUESTION");
            });
        }

        this.debug("Question : addListeners end");
    },
    addListenersAfterOwl: function() {
        var self = this;
        this.debug('Question : addListenersAfterOwl');

        this.$zoneQuestionSupport                   = $(this.zoneQuestionSupportSelector);
        this.$radioQuestionSupport                  = $(this.radioQuestionSupportSelector);


        this.$zoneQuestionSupport.on('click', function(e) {
            self.eventZoneQuestionSupportClick($(this));
        });

        this.$radioQuestionSupport.on('change', function (e) {
            self.eventRadioQuestionChange($(this));
        });

    },

    /*
     * Event
     */

    eventFileReset: function (reset, e) {
        var file = reset.siblings(this.fileQuestionSelector);
        file.wrap('<form>').closest('form').get(0).reset();
        file.unwrap();

        this.eventFileChange(file);
    },

    eventFileChange: function (fileInput) {
        if(fileInput.val() != "") {
            fileInput.siblings(this.fileQuestionNameSelector).text(fileInput.val());
            var nextFileInput = false;
            this.$fileQuestion.each(function(i,c) {
                var c = $(c);
                if (c.val() == "" && c.parents('.fileUpload').first().hasClass('hidden') && !nextFileInput) {
                    c.parents('.fileUpload').first().removeClass('hidden');
                    nextFileInput = true;
                }
            });
        } else {
            fileInput.siblings(this.fileQuestionNameSelector).text("Vide");

        }
    },

    eventSelectQuestionMatiereChange: function(select) {
        var matiere = this.$selectQuestionMatiere.val();
        this.$selectQuestionCompetenceArray.forEach(function(c, i, a) {
            c.addClass('hidden');
            c.attr('name', "");
        });
        this.$selectQuestionCompetenceArray[matiere].removeClass('hidden');
        this.$selectQuestionCompetenceArray[matiere].attr('name', this.$selectQuestionCompetenceArray[matiere].data('name'));

    },

    eventZoneQuestionSupportClick: function(zone) {
        this.$zoneQuestionSupport.removeClass(this.selectedClass);
        zone.addClass(this.selectedClass);
        zone.find(this.radioQuestionSupportSelector).first().prop("checked", true).trigger('change');
        this.openTabQuestionConsultation(false);
    },

    eventRadioQuestionChange: function (radio) {
        this.openTabQuestionMaQuestion(false);
    },


    openTabQuestionConsultation: function(button) {
        this.$buttonQuestionConsultation.addClass('open');
        this.$tabQuestionConsultation.addClass('open');
        this.$buttonQuestionMaQuestion.removeClass('open');
        this.$tabQuestionMaQuestion.removeClass('open');
    },

    openTabQuestionMaQuestion: function(button) {
        this.$buttonQuestionConsultation.removeClass('open');
        this.$tabQuestionConsultation.removeClass('open');
        this.$buttonQuestionMaQuestion.addClass('open');
        this.$tabQuestionMaQuestion.addClass('open');
    },

    debug: function(t) {
        App.debug(t);
    }
};

'use strict';
