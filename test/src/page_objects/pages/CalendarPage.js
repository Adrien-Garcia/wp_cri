"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path:'/calendrier-des-formations',
    title:'Formations | ',
    components: {
        // nav: Navigation_Bar,
        // veilles_juridiques: Accordion_

    },
    locators: {
        // Empty
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        HomePage: {
            path: pageData.path,
            title: pageData.title
        }
    }
);
/******************************************************************************/
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
const WebPage = require('./WebPage');
const debug = 0;
/**
 * Creates an instance of HomePage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True if user has authenticated himself.
 */
function HomePage (webdriver, isAuthenticated) {
    if (!(this instanceof HomePage))
        throw new SyntaxError("HomePage constructor needs to be called with the 'new' keyword.");
    WebPage.call(this, webdriver, isAuthenticated);
    // Basic information for HomePage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
    if(debug)
        console.log(util.inspect(this, true, 0, true));
} // <== End of HomePage constructor

// Prototype linkage
HomePage.prototype = Object.create(WebPage.prototype);
HomePage.prototype.constructor = HomePage;

// /** @module ~/src/page_objects/pages/HomePage */
module.exports.class = HomePage;
