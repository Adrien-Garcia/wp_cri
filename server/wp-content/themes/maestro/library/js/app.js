/**
 * Created by chorgues on 15/10/2015. 
 * Based on mdelantes work.
 */

var App = {};
var $ = jQuery;

App = {
    debug: function (t) {
        console.log(t);
    }
}

jQuery(document).ready(function($) {
    App.Home.init();
    App.Login.init();
    if($(".page-mon-compte").length > 0) {
    	App.Account.init();
    }
    
});