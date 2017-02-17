"use strict";
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until,
    WebElement = require('selenium-webdriver').WebElement;
const util = require('util');

const debug = 0;

let componentData = {
    locators: {
        date_day: '.jour',
        date_month: '.mois',
        date_year: '.annee'
    },
    helpers: {
        getDay: function() {
            return date_day.getText()
                .then(function(text) {
                    return text;
                });
        },
        getMonth: function() {
            return date_month.getText()
                .then(function(text) {
                    return text;
                });
        },
        getYear: function() {
            return date_year.getText()
                .then(function(text) {
                    return text;
                });
        },
        getFullDate: function() {
            let that = this, date = Object.create();
            return this.getDay()
            .then(function(day) {
                date.day = day;
                return that.getMonth();
            }).then(function(month) {
                date.month = month;
                return that.getYear();
            }).then(function(year) {
                date.year = year;
                return date;
            });
        }
    }
};

function DateBlock (webdriver, selector) {
    WebComponent.call(this, webdriver, componentData, selector);
}

// Prototype linkage
DateBlock.prototype = Object.create(WebComponent.prototype);
DateBlock.prototype.constructor = DateBlock;

module.exports = DateBlock;
