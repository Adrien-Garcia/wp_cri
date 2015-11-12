'use strict';

App.Question = {

    selectQuestionMatiereSelector       : '.js-question-select-matiere',
    selectQuestionCompetenceSelector    : '.js-question-select-competence',

    selectQuestionCompetenceName        : 'question_competence',

    $selectQuestionMatiere              : null,
    $selectQuestionCompetence           : null,
    $selectQuestionCompetenceArray      : [],


    init: function() {
        this.debug("Question : init start");

        var self = this;

        this.$selectQuestionMatiere               = $(this.selectQuestionMatiereSelector);
        this.$selectQuestionCompetence            = $(this.selectQuestionCompetenceSelector);

        this.$selectQuestionCompetence.each(function(i) {
            self.$selectQuestionCompetenceArray[$(this).data('matiere-id')] = $(this);
        }); 

        this.addListeners();
        this.debug("Question : init end");

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

    debug: function(t) {
        App.debug(t);
    }
};