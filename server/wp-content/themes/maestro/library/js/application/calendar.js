'use strict';
/* global App, jsvar, enquire */

App.Calendar = {

    ellispisSelector: '.js-calendar-ellipsis',

    sessionSelector: '.js-calendar__session',
    sessionBlockSelector: '.js-calendar__session-block',
    sessionBlockCloseSelector: '.js-calendar__session-block-button--close',


    $ellipsis: null,
    $session: null,
    $sessionBlock: null,
    $sessionCloseBlock: null,

    init: function () {
        var self = this;
        this.debug('init start');

        this.$ellipsis = $(this.ellispisSelector);
        this.$session = $(this.sessionSelector);
        this.$sessionBlock = $(this.sessionBlockSelector);
        this.$sessionCloseBlock = $(this.sessionBlockCloseSelector);

        enquire.register('screen and (min-width: 1240px)', function () {
            self.$ellipsis.each(function () {
                App.Utils.multilineEllipsis(this);
            });
        }).register('screen and (max-width: 1239px)', function () {
            self.$ellipsis.each(function () {
                App.Utils.unEllipsis(this);
            });
        });

        this.addListeners();

        this.debug('init end');
    },

    /*
     * Listeners for the Home page events
     */

    addListeners: function () {
        var self = this;

        this.debug('addListeners start');

        this.$session.on('click.calendar.showSession', function () {
            self.sessionClickListeners(this);
        });

        this.$sessionCloseBlock.on('click', function () {
            self.sessionBlockClose();
        });

        this.debug('addListeners end');
    },

    sessionClickListeners: function (session) {
        var self = this;
        var $session = $(session);
        var $block = $('#' + $session.data('block'));
        var content = $session.find('.calendar__session-content').html();

        this.sessionBlockClose();
        $block.find('.calendar__session-block-content').html(content);
        $block.addClass('calendar__session-block--open');

        /**
         *  Cet event est supprimé à chaque 'fermeture' de session
         *  @see sessionBlockClose
         *  */
        $(document).on('click.calendar.hideSession', function (e) {
            /**
             * if click outside the current session
             */
            if (!$session.is(e.target) && $session.has(e.target).length === 0) {
                self.sessionBlockClose();
            }
        });
    },

    sessionBlockClose: function () {
        this.$sessionBlock.removeClass('calendar__session-block--open');
        this.$sessionBlock.find('calendar__session-block-content').attr('style', '').html('');
        $(document).off('click.calendar.hideSession');
    },

    debug: function (t) {
        App.debug('Calendar : ' + t);
    },
};
