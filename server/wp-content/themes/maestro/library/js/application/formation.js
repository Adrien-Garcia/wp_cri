'use strict';

App.Formation = {

    tabFormationsFuturesSelector          : '.js-tab-formations-futures',
    tabFormationsPasseesSelector          : '.js-tab-formations-passees',

    eventTabFormationFuturesOpenSelector  : '.js-tab-formations-futures-open',
    eventTabFormationPasseesOpenSelector  : '.js-tab-formations-passees-open',

    $tabFormationsFuturesButton           : null,
    $tabFormationsPaseesButton            : null,

    $tabFormationsFutures                 : null,
    $tabFormationsPassees                 : null,


    init: function() {

        this.debug("Formation : init start");

        this.$tabFormationsFutures          = $(this.tabFormationsFuturesSelector);
        this.$tabFormationsFuturesButton    = $(this.eventTabFormationFuturesOpenSelector);
        this.$tabFormationsPassees          = $(this.tabFormationsPasseesSelector);
        this.$tabFormationsPasseesButton    = $(this.eventTabFormationPasseesOpenSelector);

        this.addListeners();

        this.debug("Formation : init end");
    },

    /*
     * Listeners for the Formation page events
     */

    addListeners: function() {
        var self = this;

        this.debug("Formation : addListeners start");

        this.$tabFormationsFuturesButton.on("click", function(e) {
            self.eventTabFormationsFuturesOpen($(this));
        });

        this.$tabFormationsPasseesButton.on("click", function(e) {
            self.eventTabFormationsPasseesOpen($(this));
        });

        this.debug("Formation : addListeners end");
    },


    /*
     * Event for changing the tab on Formation page to formations futures
     */

    eventTabFormationsFuturesOpen: function() {
        this.$tabFormationsFutures.addClass('open');
        this.$tabFormationsFuturesButton.addClass('open');
        this.$tabFormationsPassees.removeClass('open');
        this.$tabFormationsPasseesButton.removeClass('open');
    },

    /*
     * Event for changing the tab on Formation page to formations passées
     */

    eventTabFormationsPasseesOpen: function() {
        this.$tabFormationsPassees.addClass('open');
        this.$tabFormationsPasseesButton.addClass('open');
        this.$tabFormationsFutures.removeClass('open');
        this.$tabFormationsFuturesButton.removeClass('open');
    },

    debug: function(t) {
        App.debug(t);
    }
}