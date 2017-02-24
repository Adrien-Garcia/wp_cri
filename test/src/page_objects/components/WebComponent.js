"use strict";
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until,
    WebElement = require('selenium-webdriver').WebElement;
const util = require('util');

const debug = 0;

function WebComponent (webdriver, data, selector) {
    Object.assign(this, JSON.parse(JSON.stringify(data)));
    this.driver = webdriver;
    this.selector= selector;
} // End of WebComponent constructor

// Prototype linkage
WebComponent.prototype = Object.create(WebElement.prototype);
WebComponent.prototype.constructor = WebComponent;
WebComponent.prototype.init = function() {
    let that = this;

    let elementNames = [];
    let elementPromises = [];

    return this.driver.findElement(By.css(that.selector))
        .then(function (webElem) {
            return that.self = webElem;
        }, function() {
            throw new Error('Could not resolve element ' + selector);
        }).then(function () {

            for(let locator in that.locators) {
                // console.log(that.locators[locator]);
                let elements = that.driver.findElements(By.css(that.locators[locator]));
                if (elements.length === 0) {
                    throw new Error('No element found for ' + that.locators[locator]);
                } else if (elements.length === 1) {
                    elementPromises.push(elements[0]);
                } else {
                    elementPromises.push(elements);
                }
                elementNames.push(locator);

            }

            for (let fn in that.helpers) {
                Object.getPrototypeOf(that)[fn] = that.helpers[fn];
                // or no-override:
                // if(!Object.getPrototypeOf(this).hasOwnProperty(this.helpers[fn].name)) {
                //     Object.getPrototypeOf(this)[fn] = this.helpers[fn];
                // } else {
                //     throw Error();
                // }
            }

            return Promise.all(elementPromises).then(function(elements) {
                return elementPromises = elements;
            });
        }).then(function() {
            for(let index in elementPromises) {
            // elementPromises.forEach(function(index) {
            //     console.log(elementPromises[index]);
                that[elementNames[index]] = elementPromises[index];
            }


            return ;
        }).catch(function (err) {
            throw err;
        });

};

module.exports = WebComponent;
