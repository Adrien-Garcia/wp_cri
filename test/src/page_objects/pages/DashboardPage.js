"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/customer/account/',
    title: 'Mon compte',
    components: {
        AccountSideView: true
    },
    locators: {
        tab_list: '.main .block.block-account .block-content ul > li'
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        DashboardPage: {
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
 * Creates an instance of DashboardPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 */
function DashboardPage (webdriver) {
	if (!(this instanceof DashboardPage))
    	throw new SyntaxError("DashboardPage constructor needs to be called with the 'new' keyword.");
    // Webpage abstract constructor
    WebPage.call(this, webdriver, true);
    // Basic information for DashbaordPage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}

// Prototype linkage
DashboardPage.prototype = Object.create(WebPage.prototype);
DashboardPage.prototype.constructor = DashboardPage;

DashboardPage.prototype.dash = function() {
	console.log('Dash dash!');
	return this;
}

// /** @module ~/src/page_objects/pages/DashboardPage */
module.exports.class = DashboardPage;
