'use strict';
/* global App, jsvar */

App.Formation = {

    formSelector: '.js-formation-formulaire',
    errorSelector: '.js-formation-error',
    popupValideSelector: '.js-formation-formulaire-popup',

    $form: null,
    $error: null,
    $popupValide: null,

    init: function () {
        this.debug('init start');

        this.$form = $(this.formSelector);
        this.$error = $(this.errorSelector);
        this.$popupValide = $(this.popupValideSelector);

        this.$popupValide.popup({
            transition: 'all 0.3s',
            scrolllock: true,
            opacity: 0.8,
            color: '#324968',
            offsettop: 10,
            vertical: top,
        });

        this.addListeners();
        this.debug('init end');
    },


    /*
     * Listeners for the Formation page events
     */
    addListeners: function () {
        var self = this;

        this.debug('addListeners start');

        self.$form.on('submit', function (e) {
            e.preventDefault();
            self.sendFormulaireAjax();
            return false;
        });

        this.debug('addListeners end');
    },

    sendFormulaireAjax: function () {
        var self = this;
        this.debug(self.$form.serialize());
        $.ajax({
            url: self.$form.first().data('ajax'),
            data: self.$form.first().serialize(),
            success: function (_data) {
                var data = JSON.parse(_data);
                if (data.valid) {
                    self.$popupValide.popup('show');
                    self.$error.removeClass('show');
                }
                if (data.error) {
                    self.$error.html(data.error);
                    self.$error.addClass('show');
                }
                self.debug('Form send');
            },
        });
    },

    debug: function (t) {
        App.debug('Formation : ' + t);
    },
};
