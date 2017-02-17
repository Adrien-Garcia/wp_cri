'use strict';
/* global App, jsvar, enquire */

App.Calendar = {
    offsetMobileBlock: 50,
    offsetDesktopBlock: 45,

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
            $('.calendar__day-sessions--scrollable').each(function () {
                var scrollable = this;
                var rapport = $(scrollable).innerHeight() / scrollable.scrollHeight;
                scrollable.calendarScrollbar = $(scrollable).parent().find('.calendar__day-sessions-scrollbar--bar').first();
                $(scrollable.calendarScrollbar).css('height', (rapport * 100) + '%');
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
            self.sessionClickEvent(this);
        });

        this.$sessionCloseBlock.on('click', function () {
            self.sessionBlockClose();
        });

        $('.calendar__day-sessions--scrollable').on('scroll', function () {
            self.sessionsScrollEvent(this);
        });

        this.debug('addListeners end');
    },

    sessionClickEvent: function (session) {
        var self = this;
        var $session = $(session);
        var $block = $('#' + $session.data('block'));
        var content = $session.find('.calendar__session-content').html();
        var off = self.offsetDesktopBlock;
        var mobile = false;

        this.sessionBlockClose();
        $block.find('.calendar__session-block-content').html(content);
        $block.addClass('calendar__session-block--open');
        $session.addClass('calendar__session--open');

        if ($(window).width() < 1240) {
            off = $session[0].offsetTop + self.offsetMobileBlock;
            mobile = true;
        } else {
            off = $session[0].offsetTop + self.offsetDesktopBlock;
            off -= $block.outerHeight() / 2;
        }
        $block.css('top', off + 'px');
        if (mobile) {
            window.setTimeout(function () {
                App.Utils.scrollTop(700, '#' + $block[0].id, -self.offsetMobileBlock);
            }, 350);
        }

        /**
         *  Cet event est supprimé à chaque 'fermeture' de block session
         *  @see sessionBlockClose
         *  */
        $(document).on('click.calendar.hideSession', function (e) {
            /**
             * if click outside the current session and block
             */
            if (
                !$session.is(e.target)
                && $session.has(e.target).length === 0
                && !$block.is(e.target)
                && $block.has(e.target).length === 0
            ) {
                self.sessionBlockClose();
            }
        });

    },

    sessionsScrollEvent: function (scrollable) {
        var scrollbar = scrollable.calendarScrollbar;
        var scrollbarH = $(scrollbar).height();
        var rapport = scrollable.scrollTop / scrollable.scrollHeight;
        var top = scrollbarH * rapport;
        $(scrollbar).css('top', top + 'px');
    },

    sessionBlockClose: function () {
        this.$session.removeClass('calendar__session--open');
        this.$sessionBlock.removeClass('calendar__session-block--open');
        this.$sessionBlock
            .find('calendar__session-block-content').html('');
        $(document).off('click.calendar.hideSession');
    },

    debug: function (t) {
        App.debug('Calendar : ' + t);
    },
};
