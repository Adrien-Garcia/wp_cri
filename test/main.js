"use strict";

// Utility
const util = require('util'),
    fs = require('fs');

// Selenium WebDriver
const webdriver = require('selenium-webdriver'),
    test = require('selenium-webdriver/testing'),
    // Promise = webdriver.Promise,
    By = webdriver.By,
    until = webdriver.until;
const firefox = require('selenium-webdriver/firefox'),
    chrome = require('selenium-webdriver/chrome');

// Page Objects pattern
const User = require('./src/page_context').User;
const page_index = require('./src/page_context').page_index;

// Global scope variables
var builder, driver, myUser;
const debug = 0;
const WINDOW_MIN_WIDTH=800,
    WINDOW_MIN_HEIGHT=500;
const DISPLAY_TRANSITION_WIDTH=763; // limit between desktop and tablet display
console.log('Base host URL: '+util.inspect(process.env.BASE_HOST_URL, true, 1, true));

// Browser config (generated through 'jetdocker test')
const browserName = process.env.SEL_BROWSER_NAME,
    browserVersion = process.env.SEL_BROWSER_VERSION,
    browserPlatform = process.env.SEL_BROWSER_PLATFORM,
    browser = process.env.SELENIUM_BROWSER,
    remoteUrl = process.env.SELENIUM_REMOTE_URL;
if(debug){
    console.log('browser: ' + browserName);
    console.log('version: ' + browserVersion);
    console.log('platform: ' + browserPlatform);
    console.log('Selenium hub: ' + (remoteUrl ? remoteUrl : 'none'));
}

// NOTE Move to User API ?
// Classic use : setupBrowser(myUser.driver,  myUser, done)
const setupBrowser = function(driver, user, callback) {
    driver
    .manage()
    .deleteAllCookies() // Cleans up temporary files
    .then(function() {
        return user.init(); // Refreshes landing page in browser and prepare data
    }).then(function() {
        callback();
    }).catch(function(err) {
        return callback(err);
    });
};

const shutDownBrowser = function(driver, callback) {
    driver
    .quit()
    .then(function() {
        callback();
    }).catch(function(err) {
        return callback(err);
    });
};

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

        shutDownBrowser(driver, done);
    });

    // SUBSUITES
    test.describe('Given user is not authenticated', function() {
        beforeEach(function(done) {
            setupBrowser(driver, myUser, done);
        });


        test.describe('Login panel', function() {
            test.it('should open when clicking dashboard button', function(done) {
                done();
            });

        }); // End of suite 'Guest user'

    });

});
