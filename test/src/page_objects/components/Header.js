"use strict";

const WebComponent = require('./WebComponent');

const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;

let componentData = {
    // page: null,
    locators: {
        login_panel_link: '.sel-open-onglet-connexion'
        ,
        crpcen_input: '#loginFieldId',
        password_input: '#passwordFieldId',
        submit_button: '.sel-submit_button-login'
    },
    helpers: {
        openLoginPanel: function() {
            return this.login_panel_link.click();
        }
    }
};

function Header (webdriver, selector) {
    webdriver.findElement(By.css('.sel-open-onglet-connexion')).click();
    WebComponent.call(this, webdriver, componentData, selector);
    // webdriver.findElement(By.css('.sel-open-onglet-connexion')).click()
    //     .then(function() {
    //         WebComponent.call(this, webdriver, componentData, selector);
    //         return;
    //     });
}

// Prototype linkage
Header.prototype = Object.create(WebComponent.prototype);
Header.prototype.constructor = Header;

// TODO Implement generic form
Header.prototype.isLoginPanelOpened = function() {
    return this.driver.findElement(By.css(componentData.locators.login_panel_link))
        .then(function() {
            return true;
        }, function(){
            return false;
        }).catch(function(err) {
            throw err;
        });
};

Header.prototype.openLoginPanel = function() {
    return this.isLoginPanelOpened()
        .then(function(visible) {
            if(!visible) {
                return this.login_panel_link.click();
            }
            return;
        }).catch(function(err) {
            throw err;
        });
};

Header.prototype.clearCredentials = function () {
    let that = this;
    return that.crpcen_input.clear()
        .then(function() {
            that.password_input.clear();
        });
};

Header.prototype.logUser = function(username, password) {
    let that = this;

    // const util = require('util');
    // console.log(util.inspect(that, true, 2, true));

    return this.openLoginPanel()
        .then(function() {
            return that.crpcen_input.sendKeys(username);
            // return that.driver.findElement(By.css('#loginFieldId')).sendKeys(username);
        }).then(function() {
            return that.password_input.sendKeys(password);
        }).then(function() {
            return that.submit_button.click();
        }).catch(function(err) {
            throw err;
        });
};

module.exports = Header;
