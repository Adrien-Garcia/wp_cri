"use strict";
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;
const util = require('util');

const debug = 0;

function WebPage (webdriver, data) {
    if (!(this instanceof WebPage))
        throw new SyntaxError(this.constructor.name + " constructor needs to be called with the 'new' keyword.");
    this.driver = webdriver;
    Object.assign(this, JSON.parse(JSON.stringify(data)));
}

// Prototype linkage
WebPage.prototype = Object.create(Object.prototype);
WebPage.prototype.constructor = WebPage;

WebPage.prototype.init = function() {
    let that = this;

    return this.initComponents()
        .then(function() {
            // DOM selectors storage: ['name'] => 'cssSelector'
            if(debug)
                console.log('Const: ' + util.inspect(that.locators, true, 1, true));
            let elemNames = [], elemPromises = [];
            for(let prop in that.locators) {
                if(debug)
                    console.log('Element ' + prop + ' is being added to page object...');
                elemNames.push(prop);
                elemPromises.push(
                    that.driver.wait(function() {
                        return that.driver.findElements(By.css(that.locators[prop]))
                            .then(function(elements) {
                                return elements;
                            }, function(err) {
                                console.log('have not found element ' + prop + ' yet');
                                return false;
                            })
                    }, 6000, "Timeout exception in DOM initialization")
                        .then(function(elements) {
                            if(debug)
                                console.log(util.inspect(elements[0], true, 0, true));
                            return elements;
                        })
                );
            }
            // Resolve all WebElementPromises
            return Promise.all(elemPromises)
                .then(function(webElements) {
                    return ([elemNames, webElements]);
                }, function(err) {
                    // Should never be reached since WebDriver.findElements(...) always
                    // returns a fulfilled Promise (unlike Webdriver.findElement(...))
                    throw err;
                });
        }).then(function([names, elements]) {
            for(let index in elements) {
                switch(elements[index].length) {
                    case 1: that[names[index]] = (elements[index][0]);
                        break;
                    case 0:
                        throw new WebElementNotFoundException({CSS2: that.locators[names[index]]});
                    default: that[names[index]] = elements[index];
                }
            }
            if(debug) {
                console.log('Ended DOM elements initiation successfully, final object is :');
                that.print();
            }
            return that;
        });
};
/**Initializes the current WebPage with its attributes
 * @private
 * @returns {Thenable<Object>}
 */
WebPage.prototype.initComponents = function() {
    let that = this;

    this.isAuthenticated = undefined;

    return this.checkAuthenticationStatus()
        .then(function(authStatus) {
            if(authStatus === true || authStatus === false) {
                return authStatus;
            } else {
                throw new Error('Could not detect authentication status (returned ' + authStatus +')');
            }
        }).then(function() {
            // console.log(that.constructor.name+'__: '+util.inspect(that, true, 2, true));
            // console.dir(that.components);

            Object.keys(that.components).forEach(function(componentClass) {
                Object.keys(that.components[componentClass]).forEach(function(component) {
                    let comp = new (require('../components/'+componentClass))(that.driver, that.components[componentClass][component]);
                    return comp.init()
                        .then(function() {
                            return that[component] = comp;
                        });
                });
            });
            return that;
        });
};

WebPage.prototype.checkAuthenticationStatus = function() {
    let that = this;
    let loginButton = this.driver.findElement(By.css(".sel-open-onglet-connexion"));

    return this.driver.actions()
        .mouseMove(loginButton)
        .perform()
        .then(function() {
            return that.driver.findElement(By.css('a[href="/wp-login.php?action=logout"]'));
        }).then(function() {
            return true;
        }, function() {
            return false;
        }).catch(function(err) {
            throw err;
        });

};

module.exports = WebPage;
