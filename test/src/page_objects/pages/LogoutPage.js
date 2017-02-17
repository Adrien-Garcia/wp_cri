"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/wp-login.php?action=logout',
    title: 'Avertissement d’échec de WordPress',
    components: {
        // Default
	},
    locators: {
      confirm_logout_link: '.sel-confirm_logout_link-logout'
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        LogoutPage: {
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
 * Creates an instance of LoggedOutPage.
 * @constructor
 * @requires {WebPage}
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 */
function LogoutPage (webdriver) {
	if (!(this instanceof LogoutPage))
    	throw new SyntaxError("LogoutPage constructor needs to be called with the 'new' keyword.");
    // Webpage abstract constructor
    WebPage.call(this, webdriver, false);
	// Basic information for HomePage
	Object.assign(this, JSON.parse(JSON.stringify(pageData)));
	if(debug)
    	console.log(util.inspect(this, true, 0, true));
} // <== End of HomePage constructor

// Prototype linkage
LogoutPage.prototype = Object.create(WebPage.prototype);
LogoutPage.prototype.constructor = LogoutPage;

module.exports.class = LogoutPage;
