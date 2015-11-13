'use strict';

App.Question = {

    selectQuestionMatiereSelector       : '.js-question-select-matiere',
    selectQuestionCompetenceSelector    : '.js-question-select-competence',

    zoneQuestionSupportSelector         : '.js-question-support-zone',
    radioQuestionSupportSelector        : '.js-question-support-radio',

    selectQuestionCompetenceName        : 'question_competence',

    tabQuestionConsultationSelector     : '.js-question-tab-consultation',
    tabQuestionMaQuestionSelector       : '.js-question-tab-ma-question',
    buttonQuestionConsultationSelector  : '.js-question-button-consultation',
    buttonQuestionMaQuestionSelector    : '.js-question-button-ma-question',

    $selectQuestionMatiere              : null,
    $selectQuestionCompetence           : null,
    $selectQuestionCompetenceArray      : [],
    $zoneQuestionSupport                : null,

    $tabQuestionConsultation            : null,
    $tabQuestionMaQuestion              : null,
    $buttonQuestionConsultation         : null,
    $buttonQuestionMaQuestion           : null,


    init: function() {
        this.debug("Question : init start");

        var self = this;

        this.$selectQuestionMatiere                 = $(this.selectQuestionMatiereSelector);
        this.$selectQuestionCompetence              = $(this.selectQuestionCompetenceSelector);

        this.$selectQuestionCompetence.each(function(i) {
            self.$selectQuestionCompetenceArray[$(this).data('matiere-id')] = $(this);
        });

        this.$tabQuestionConsultation               = $(this.tabQuestionConsultationSelector);
        this.$tabQuestionMaQuestion                 = $(this.tabQuestionMaQuestionSelector);
        this.$buttonQuestionConsultation            = $(this.buttonQuestionConsultationSelector);
        this.$buttonQuestionMaQuestion              = $(this.buttonQuestionMaQuestionSelector);

        this.owlCarouselInit(this.initAfterOwl.bind(this));

        this.addListeners();
        this.debug("Question : init end");

    },

    initAfterOwl: function() {
        var self = this;
        this.$zoneQuestionSupport                   = $(this.zoneQuestionSupportSelector);

        this.$zoneQuestionSupport.on('click', function(e) {
            self.eventZoneQuestionSupportClick($(this));
        });
    },


    /*
     * Listeners for the Question page events
     */

    addListeners: function() {
        var self = this;

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
        
        this.debug("Question : addListeners end");
    },

    /*
     * Event 
     */

    eventSelectQuestionMatiereChange: function(select) {
        var matiere = this.$selectQuestionMatiere.val();
        this.$selectQuestionCompetenceArray.forEach(function(c, i, a) {
            c.addClass('hidden');
        });
        this.$selectQuestionCompetenceArray[matiere].removeClass('hidden');
    },

    eventZoneQuestionSupportClick: function(zone) {
        zone.find(this.radioQuestionSupportSelector).first().prop("checked", true).trigger('change');
        this.openTabQuestionConsultation(false);
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


    owlCarouselInit: function(cbInit) {
        $("#owl-support").owlCarousel({
      
            pagination: false,
            dots: false,
            navText: false,
            itemClass: 'owl-item js-question-support-zone',
            onInitialized: cbInit,
            responsive:{
                0 : {
                    items:1,
                    dots:true,

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

    debug: function(t) {
        App.debug(t);
    }
};