'use strict';

App.Utils = {

    queryString                 : {},


    init: function() {
        this.debug("Utils : init start");

        this.queryString = this.getQueryString();

        this.debug("Utils : init end");

    },


    getQueryString: function() {
        var query_string = {};
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        var arr = [];
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            // If first entry with this name create a key for it
            if (typeof query_string[pair[0]] === "undefined") {
                query_string[pair[0]] = decodeURIComponent(pair[1]);
                // If second entry with this name create an array and remove the previously created key
            } else if (typeof query_string[pair[0]] === "string") {
                arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
                query_string[pair[0]] = arr;
                // If third or later entry with this name add to array
            } else {
                query_string[pair[0]].push(decodeURIComponent(pair[1]));
            }
        }
        return query_string;
    },

    debug: function(t) {
        App.debug(t);
    }
};