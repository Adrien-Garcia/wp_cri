"use strict";

const WebComponent = require('./WebComponent');

let componentData = {
    // page: null,
    locators: {
        login_panel_link: '.sel-open-onglet-connexion'
    },
    helpers: {
        openLoginPanel: function() {
            return this.login_panel_link.click();
        }
    }
};

function Header (webdriver, selector) {
    WebComponent.call(this, webdriver, componentData, selector);
}

// Prototype linkage
Header.prototype = Object.create(WebComponent.prototype);
Header.prototype.constructor = Header;

module.exports = Header;
