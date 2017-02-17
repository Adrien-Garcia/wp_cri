"use strict";
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until,
    WebElement = require('selenium-webdriver').WebElement;
const util = require('util');

const debug = 0;

function WebComponent (webdriver, data, selector) {
    Object.assign(this, JSON.parse(JSON.stringify(data)))
    this.driver = webdriver;
    this.selector= selector;
} // End of WebComponent constructor

// Prototype linkage
WebComponent.prototype = Object.create(WebElement.prototype);
WebComponent.prototype.constructor = WebComponent;
WebComponent.prototype.init = function() {
    let that = this;

    return this.driver.findElement(By.css(selector))
        .then(function (webElem) {
            return that.self = webElem;
        }, function() {
            throw new Error('Could not resolve element ' + selector);
        }).then(function () {
            for(let locator in this.locators) {
                let elements = that.self.findElements(this.locators[locator]);
                if (elements.length === 0) {
                    throw new Error('No element found for ' + this.locators[locator]);
                } else if (elements.length === 1) {
                    Promise.resolve();
                } else {
                    Promise.all([]);
                }
            }

            for (let fn in this.helpers) {
                Object.getPrototypeOf(this)[fn] = this.helpers[fn];
                // or no-override:
                // if(!Object.getPrototypeOf(this).hasOwnProperty(this.helpers[fn].name)) {
                //     Object.getPrototypeOf(this)[fn] = this.helpers[fn];
                // } else {
                //     throw Error();
                // }
            }

            return;
        }).catch(function (err) {
            throw err;
        });



};

module.exports = WebComponent;
