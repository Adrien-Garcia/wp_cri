'use strict';
/**
 * Created by chorgues on 15/10/2015. 
 * Based on mdelantes work.
 */

var App = {};
if (typeof $ === 'undefined') {
    window.$ = jQuery;
}

App = {
    debug: function (t) {
        // eslint-disable-next-line no-console
        console.log(t);
    },
};

jQuery(document).ready(function ($) {
    App.Utils.init();
    App.Home.init();
    App.Login.init();
    if ($('.page-mon-compte').length > 0) {
        App.Account.init();
    }
    App.Question.init();
    App.Cridonline.init();
    if ($('.page-calendar').length > 0) {
        App.Calendar.init();
    }
});
