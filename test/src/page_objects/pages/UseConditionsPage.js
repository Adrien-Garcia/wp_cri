"use strict";
/***************************** PAGE DEFINITION ********************************/
const pageData = {
    path: '/utilisation-des-cookies/', // host == process.env.BASE_HOST_URL
    title: 'Utilisation des cookies - Cridon Lyon',
    locators: {
        // Empty
    },
    components: [
        // Default
    ],
    data: {
        // Empty
    }
};
/******************************* INDEX EXPORT *********************************/
module.exports.index = Object.assign({},
    {
        UseConditionsPage: {
            path: pageData.path,
            title: pageData.title
            // Full URL is defined into ~/test/src/page_context.js
        }
    }
);
/************************* IMPORTS AND SETUP **********************************/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
const WebPage = require('./WebPage'); // abstract model for a web page
// Custom errors/exceptions libraries
// const CustomExceptions = require('../../custom_exceptions');
// Other variables
const debug = 0;
/*************************** CONSTRUCTOR **************************************/
/**Creates an instance of UseConditionsPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function UseConditionsPage (webdriver, isAuthenticated) {
	if (!(this instanceof UseConditionsPage))
    	throw new SyntaxError(
            "UseConditionsPage constructor needs to be called with the 'new' keyword."
        );

    // WebPage abstract constructor
    WebPage.call(this, webdriver, isAuthenticated);

    // Attaching basic data to UseConditionsPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
} // End of UseConditionsPage constructor
/************************** PROTOTYPE CHAIN ***********************************/
UseConditionsPage.prototype = Object.create(WebPage.prototype);
UseConditionsPage.prototype.constructor = UseConditionsPage;
/************************* SPECIFIC METHODS ***********************************/
UseConditionsPage.prototype.myFunc = function() {
	if(debug)
        console.log('This is my prototype\'s function!');
	return this;
};
/*************************** CLASS EXPORT *************************************/
module.exports.class = UseConditionsPage;
