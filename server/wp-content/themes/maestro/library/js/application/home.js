'use strict';

App.Home = {

    flashBlockSelector              : '.js-flash-info',
    tabVeilleSelector               : '.js-tab-veille',
    tabFormationSelector            : '.js-tab-formation',
    accordionContentSelector        : '.js-accordion-content',

    eventFlashOpenSelector          : '.js-flash-open',
    eventFlashCloseSelector         : '.js-flash-close',
    eventTabVeilleOpenSelector      : '.js-tab-veille-open',
    eventTabFormationOpenSelector   : '.js-tab-formation-open',
    eventAccordionOpenSelector      : '.js-accordion-button',

    $flashBlock                     : null,
    $flashToggle                    : null,
    $tabVeille                      : null,
    $tabVeilleButton                : null,
    $tabFormation                   : null,
    $tabFormationButton             : null,
    $accordionContent               : null,

    init: function() {

        this.debug("Home : init start");

        this.$flashBlock            = $(this.flashBlockSelector);
        this.$flashToggle           = $(this.eventFlashOpenSelector).add(this.eventFlashCloseSelector);

        this.$tabVeille             = $(this.tabVeilleSelector);
        this.$tabVeilleButton       = $(this.eventTabVeilleOpenSelector);
        this.$tabFormation          = $(this.tabFormationSelector);
        this.$tabFormationButton    = $(this.eventTabFormationOpenSelector);

        this.$accordionButton       = $(this.eventAccordionOpenSelector);
        this.$accordionContent      = $(this.accordionContentSelector);

        this.addListeners();

        this.debug("Home : init end");

    },

    /*
     * Listeners for the Home page events
     */

    addListeners: function() {
        var self = this;

        this.debug("Home : addListeners start");

        this.$flashToggle.on("click", function(e) {
           self.eventFlashToggle($(this));
        });

        this.$tabVeilleButton.on("click", function(e) {
            self.eventTabVeilleOpen($(this));
        });

        this.$tabFormationButton.on("click", function(e) {
            self.eventTabFormationOpen($(this));
        });

        this.$accordionButton.on("click", function(e) {
            self.eventAccordionOpen($(this));
        });

        this.debug("Home : addListeners end");
    },

    /*
     * Event for toggling on and off the flash 
     */

    eventFlashToggle: function() {
        this.$flashBlock.toggleClass("closed");
    },

    /*
     * Event for changing the tab on Home page to veille
     */

    eventTabVeilleOpen: function() {
        this.$tabVeille.addClass('open');
        this.$tabFormation.removeClass('open');
        this.accordionOpenFirst(this.$tabVeille);
    },

    /*
     * Event for changing the tab on Home page to formation
     */

    eventTabFormationOpen: function() {
        this.$tabFormation.addClass('open');
        this.$tabVeille.removeClass('open');
        this.accordionOpenFirst(this.$tabFormation);
    },

    /*
     * Event for switching the date displayed
     */

    eventAccordionOpen: function(src) {
        this.$accordionContent.addClass('closed');
        src.parent(this.accordionContentSelector).removeClass('closed');
    },

    accordionOpenFirst: function(parent) {
        if (parent == undefined) {
            parent = $(document);
        }
        if (!parent instanceof jQuery) {
            parent = $(parent);
        }
        this.$accordionContent.addClass('closed');
        parent.find(this.accordionContentSelector).first().removeClass('closed');


    },


    debug: function(t) {
        App.debug(t);
    }
};