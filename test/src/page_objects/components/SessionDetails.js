"use strict";
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until,
    WebElement = require('selenium-webdriver').WebElement;
const util = require('util');

const debug = 0;

let componentData = {
    element: '.calendar__session-block-content',
    locators: {
        close_button: '',
        formation_page_link: ''
    },
    helpers: {

    },
    status: {

    }
};

function SessionDetails (webdriver) {
    Object.assign(this, componentData);
    WebComponent.call(this, webdriver);
}

// Prototype linkage
SessionDetails.prototype = Object.create(WebComponent.prototype);
SessionDetails.prototype.constructor = SessionDetails;

SessionDetails.prototype.init = function() {
    for(let locator in this.locators) {
        let elements = webelement.findElements(this.locators[locator]);
        if (elements.length === 0) {
            throw new Error();
        } else if (elements.length === 1) {
            Promise.resolve();
        } else {
            Promise.all([]);
        }
    }

    for (let fn in this.helpers) {
        Object.getPrototypeOf(this)[fn] = this.helpers[fn];
    }
};

module.exports = SessionDetails;
