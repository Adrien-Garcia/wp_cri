"use strict";
/******************************* INDEX ****************************************/
const pageData = {
	path: '/',
	title: 'CRIDON Lyon : des spécialistes au service des notaires',
	components: {
        Header: {
            header: '.sel-header'
        }
	},
	locators: {
        row_01: '.row_01',
        row_02: '.row_02',
        row_03: '.row_03',
        row_04: '.row_04'
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
function HomePage (webdriver) {
	WebPage.call(this, webdriver, pageData);
} // <== End of HomePage constructor

// Prototype linkage
HomePage.prototype = Object.create(WebPage.prototype);
HomePage.prototype.constructor = HomePage;

module.exports.class = HomePage;

HomePage.prototype.answer = function() {
	return 42;
};
