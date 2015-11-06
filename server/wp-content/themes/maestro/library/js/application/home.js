'use strict';

App.Home = {

    flashBlockSelector              : '.js-flash-info',
    tabVeilleSelector               : '.js-tab-veille',
    tabFormationSelector            : '.js-tab-formation',
    accordionContentSelector        : '.js-accordion-content',
    linkBlockSelector               : '.js-home-block-link',

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
    $linkBlock                      : null,

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

        this.$linkBlock             = $(this.linkBlockSelector);

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

        this.$linkBlock.on("click", function(e) {
            self.eventLinkBlockClick($(this));
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
        this.$tabVeilleButton.addClass('open');
        this.$tabFormation.removeClass('open');
        this.$tabFormationButton.removeClass('open');
        this.accordionOpenFirst(this.$tabVeille);
    },

    /*
     * Event for changing the tab on Home page to formation
     */

    eventTabFormationOpen: function() {
        this.$tabFormation.addClass('open');
        this.$tabFormationButton.addClass('open');
        this.$tabVeille.removeClass('open');
        this.$tabVeilleButton.removeClass('open');
        this.accordionOpenFirst(this.$tabFormation);
    },

    /*
     * Event for switching the date displayed
     */

    eventAccordionOpen: function(src) {
        this.$accordionContent.addClass('closed');
        src.parent(this.accordionContentSelector).removeClass('closed');
    },

    /*
     * Event for opening the correct link on click on one of the blocks
     */

    eventLinkBlockClick: function(element) {
        var href = element.find('a').attr('href') != undefined ? element.find('a').attr('href') : '#';
        document.location.href = href;
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