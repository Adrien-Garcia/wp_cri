'use strict';

App.Formation = {

    formationFutureSelector         : '.js-tab-formation-future-open',
    formationPaseesSelector         : '.js-tab-formation-passees-open',

    $formationFuture                : null,
    $formationPasees                : null,


    init: function() {

        this.debug("Formation : init start");

        this.$formationFuture = $(this.formationFutureSelector);
        this.$formationPasees = $(this.formationPaseesSelector);

        this.addListeners();

        this.debug("Formation : init end");
    },

    /*
     * Listeners for the Formation page events
     */

    addListeners: function() {
        var self = this;

        this.debug("Formation : addListeners start");

        this.$formationFuture.on("click", function(e) {
            self.eventFormationFuture($(this));
        });

        this.$formationPasees.on("click", function(e) {
            self.eventFormationPassees($(this));
        });

        this.debug("Formation : addListeners end");
    },

    /*
     * Event for opening Formation Futur tab
     */

    eventFormationFuture: function() {
        this.$formationFuture.addClass("open");
        this.$formationPasees.removeClass("open");
    },

    /*
     * Event for opening Formation Passees tab
     */

    eventFormationPassees: function() {
        this.$formationPasees.addClass("open");
        this.$formationFuture.removeClass("open");
    },

    debug: function(t) {
        App.debug(t);
    }
}