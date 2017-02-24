"use strict";

// Utility libraries
const util = require('util');
const fs = require('fs');

// Selenium WebDriver libraries
const webdriver = require('selenium-webdriver');
const By = webdriver.By;
const until = webdriver.until;
const test = require('selenium-webdriver/testing');
const firefox = require('selenium-webdriver/firefox');
const chrome = require('selenium-webdriver/chrome');

// Page Objects pattern
const User = require('./src/page_context').User;
const page_index = require('./src/page_context').page_index;

// Global scope variables
var builder, driver, myUser;
const debug = 0;
const WINDOW_MIN_WIDTH=1000;
const WINDOW_MIN_HEIGHT = 500;
const DISPLAY_TRANSITION_WIDTH=763; // limit between desktop and tablet display
console.log('Base host URL: '+util.inspect(process.env.BASE_HOST_URL, true, 1, true));

// Browser config (generated through 'jetdocker test')
const browserName = process.env.SEL_BROWSER_NAME;
const browserVersion = process.env.SEL_BROWSER_VERSION;
const browserPlatform = process.env.SEL_BROWSER_PLATFORM;
const browser = process.env.SELENIUM_BROWSER;
const remoteUrl = process.env.SELENIUM_REMOTE_URL;

/******************************************************************************/
/********************************* TESTS **************************************/
/******************************************************************************/
test.describe('MOCHA - Tests Suite', function() {
    // SETUP

    // Mocha hooks
    before(function(done) {
        // Temporarly accepting untrusted certificate issuers
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

        if(browserName === 'firefox') {
            let profile = new firefox.Profile('./lib/klfyjbpv.seleniumProfile');
            // let profile = new firefox.Profile();
            profile.setAcceptUntrustedCerts(true);
            profile.acceptUntrustedCerts(true);
            // /!\ Uncomment to activate AdBlock Plus /!\
            // profile.addExtension('./lib/adblock_plus-2.8.2-an+fx+sm+tb.xpi');

            let options = new firefox.Options()
                .setProfile(profile);

            var wd = new webdriver.Builder()
                .forBrowser(browserName, browserVersion, browserPlatform)
                .setFirefoxOptions(options)
                .build();

            // Loading a page times out after driver.get(...) has been called
            wd.manage()
                .timeouts()
                .pageLoadTimeout(10000); // ~ 2000-15000

            // When a click() requests for a new page to get loaded
            wd.manage()
                .timeouts()
                .implicitlyWait(6000); // ~ 4000-8000

        } else { // browser != Firefox
            var wd = new webdriver.Builder()
            .withCapabilities(
                new webdriver.Capabilities()
                    .set(webdriver.Capability.BROWSER_NAME, webdriver.Browser[browserName.toUpperCase()])
                    .set(webdriver.Capability.PLATFORM, browserPlatform.toUpperCase())
                    .set(webdriver.Capability.SUPPORTS_JAVASCRIPT, true)
                    .set(webdriver.Capability.ACCEPT_SSL_CERTS, true)
            )
            .build();
        }

        Promise.resolve(wd)
        .then(function(wd) {
            driver = wd;
            return new User(driver);
        }).then(function(user) {
            return user.init()
            .then(function(user) {
                // console.log('<TRACE>');
                // console.log(util.inspect(user,  true, 4, true));
                // console.log('</TRACE>');
                return user;
            });
        }).then(function(user) {
            myUser = user;
            return driver.manage().window().getSize();
        }).then(function(size) {
            if(size.width < WINDOW_MIN_WIDTH || size.height < WINDOW_MIN_HEIGHT) {
                return driver.manage().window().setSize(WINDOW_MIN_WIDTH, WINDOW_MIN_HEIGHT);
            } else {
                return;
            }
        }).then(function() {
            return done();
        }).catch(function(err) {
            return done(err);
        });
    });


    after(function(done) {
        // Restoring untrusted certificate policy
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = process.env.SEL_DEFAULT_NODE_TLS_REJECT_UNAUTHORIZED;
        process.env.SEL_DEFAULT_NODE_TLS_REJECT_UNAUTHORIZED = undefined;

        myUser.shutDownBrowser(done);
    });

    // SUBSUITES
    test.describe('Given user is not authenticated', function() {
        before(function(done) {
            myUser.setupBrowser(done);
        });


        test.describe('Homepage', function() {
            test.it('should display header', function() {
                return myUser.isElementVisibleByCss('.sel-header')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('should display footer', function() {
                return myUser.isElementVisibleByCss('.sel-footer')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('contains link to "Vie du Cridon"', function() {
                return myUser.isElementVisibleByCss('.sel-vie_cridon_link')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('contains link to "Veille juridique"', function() {
                return myUser.isElementVisibleByCss('.sel-veille_cridon_link')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('contains link to "Cahiers du Cridon"', function() {
                return myUser.isElementVisibleByCss('.cahier.js-home-block-link')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

        }); // End of suite 'Homepage'

        test.describe('Calendar', function() {
            test.it('should ', function() {
                return myUser.isElementVisibleByCss('.sel-header')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('should display footer', function() {
                return myUser.isElementVisibleByCss('.sel-footer')
                    .then(function(visible) {
                        return visible;
                    }).catch(function(err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

        }); // End of suite 'Calendar'

        test.describe('Basic functionalities', function() {
            test.it.only('Login from homepage', function(done) {

                Promise.resolve()
                    .then(function() {
                        return myUser.logUser('69002', 'PQTR');
                    }).then(function() {
                        done();
                    }).catch(function(err) {
                        throw err;
                });

            });
        })
    });

});
