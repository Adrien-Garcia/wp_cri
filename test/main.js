"use strict";

// Utility libraries
const util = require('util');
const fs = require('fs');
// Selenium WebDriver libraries
const webdriver = require('selenium-webdriver');
const test = require('selenium-webdriver/testing');
const firefox = require('selenium-webdriver/firefox');
const chrome = require('selenium-webdriver/chrome');

const By = require('selenium-webdriver').By,
    until = require('selenium-webdriver').until;

// Chai & submodules TODO DELETE
const chai = require('chai'),
    should = chai.should(),
    expect = chai.expect,
    assert = chai.assert,
    chaiAsPromised = require('chai-as-promised');
chai.use(chaiAsPromised);

// Page Objects pattern
const User = require('./src/page_context').User;
const page_index = require('./src/page_context').page_index;

// Global scope variables
let driver, myUser;
const debug = 0;
const WINDOW_MIN_WIDTH=1000;
const WINDOW_MIN_HEIGHT = 500;
const DISPLAY_TRANSITION_WIDTH=763; // limit between desktop and tablet display
console.log('Base host URL: ' + util.inspect(process.env.BASE_HOST_URL, true, 1, true));

// Browser config (generated through 'jetdocker test')
const browserName = process.env.SEL_BROWSER_NAME;
const browserVersion = process.env.SEL_BROWSER_VERSION;
const browserPlatform = process.env.SEL_BROWSER_PLATFORM;
const browser = process.env.SELENIUM_BROWSER;
const remoteUrl = process.env.SELENIUM_REMOTE_URL;

// ORM
const Sequelize = require('sequelize');

const db_name = 'wp_cridon';
const db_user = 'root';
const db_password = 'root';
const db_host = 'localhost';
const db_port = '3306';
const db_dialect = 'mariadb';

let sequelize = new Sequelize(
    db_name,
    db_user,
    db_password,
    {
        host: db_host,
        port: db_port,
        dialect: db_dialect
    }
);

/******************************************************************************/
/********************************* TESTS **************************************/
/******************************************************************************/
test.describe('MOCHA - Tests Suite', function() {
    before(function(done) {
        let wd;

        // Temporarly accepting untrusted certificate issuers
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

        if(browserName === 'firefox') {
            console.log('Setting up for Firefox');
            // let profile = new firefox.Profile('./lib/klfyjbpv.seleniumProfile');
            let profile = new firefox.Profile('/home/agarcia/workspace/wp_cridon/test/lib/klfyjbpv.seleniumProfile');
            // let profile = new firefox.Profile();
            profile.setAcceptUntrustedCerts(true);
            profile.acceptUntrustedCerts(true);
            // /!\ Uncomment to activate AdBlock Plus /!\
            // profile.addExtension('./lib/adblock_plus-2.8.2-an+fx+sm+tb.xpi');

            let options = new firefox.Options()
                .setProfile(profile);

            wd = new webdriver.Builder()
                .forBrowser(browserName, browserVersion, browserPlatform)
                .setFirefoxOptions(options)
                .build();

            // Loading a page times out after driver.get(...) has been called
            wd.manage()
                .timeouts()
                .pageLoadTimeout(60000); // ~ 2000-15000

            // When a click() requests for a new page to get loaded
            wd.manage()
                .timeouts()
                .implicitlyWait(6000); // ~ 4000-8000

        } else { // browser != Firefox
            wd = new webdriver.Builder()
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
            }
            return;
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

    test.describe('Given user is not authenticated', function() {
        before(function (done) {
            myUser.setupBrowser(done);
        });

        test.describe('Homepage', function () {
            test.it('should display header', function () {
                return myUser.isElementVisibleByCss('.sel-header')
                    .then(function (visible) {
                        return visible;
                    }).catch(function (err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('should display footer', function () {
                return myUser.isElementVisibleByCss('.sel-footer')
                    .then(function (visible) {
                        return visible;
                    }).catch(function (err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('contains link to "Vie du Cridon"', function () {
                return myUser.isElementVisibleByCss('.sel-vie_cridon_link')
                    .then(function (visible) {
                        return visible;
                    }).catch(function (err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('contains link to "Veille juridique"', function () {
                return myUser.isElementVisibleByCss('.sel-veille_cridon_link')
                    .then(function (visible) {
                        return visible;
                    }).catch(function (err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

            test.it('contains link to "Cahiers du Cridon"', function () {
                return myUser.isElementVisibleByCss('.cahier.js-home-block-link')
                    .then(function (visible) {
                        return visible;
                    }).catch(function (err) {
                        done(err);
                    })
                    .should.eventually.be.true;
            });

        }); // End of suite 'Homepage'

        test.describe('Basic functionalities', function () {
            test.describe('Login from homepage', function () {
                beforeEach(function (done) {
                    return myUser.setupBrowser(done)
                });

                test.it('Valid credentials', function (done) {
                    Promise.resolve()
                        .then(function () {
                            driver.wait(function() {
                                return driver.findElement(By.css('#close')).isDisplayed();
                            }, 5000);
                        }).then(function () {
                            return myUser.logUser('69002', 'PQTR');
                        }).then(function () {
                            return myUser.urlEquals(page_index.DashboardPage.url);
                        }).then(function () {
                            done();
                        }).catch(function (err) {
                            throw err;
                    });

                });

                test.it('Invalid credentials', function (done) {
                    Promise.resolve()
                        .then(function () {
                            driver.wait(function() {
                                return driver.findElement(By.css('#close')).isDisplayed();
                            }, 5000);
                        }).then(function () {
                            return myUser.logUser('00000', 'AAAA');
                        }).then(function () {
                            return myUser.urlEquals(page_index.HomePage.url + '#');
                        }).then(function () {
                            done();
                        }).catch(function (err) {
                            throw err;
                    });

                });
            });

        });

    }); // End of suite 'Given user is not authenticated'

    test.describe('Given user has signed in', function() {
        before(function(done) {
            myUser.setupBrowser(done);
        });

        beforeEach(function(done) {
            myUser.driver
                .manage()
                .deleteAllCookies()
                .then(function () {
                    return myUser.init();
                }).then(function () {
                    driver.wait(function() {
                        return driver.findElement(By.css('#close')).isDisplayed();
                    }, 5000);
                }).then(function () {
                    return myUser.logUser('69002', 'PQTR');
                }).then(function () {
                    return myUser.urlEquals(page_index.DashboardPage.url);
                }).then(function () {
                    done();
                }).catch(function (err) {
                    throw err;
            });
        });

        test.describe('As an associate', function() {
            test.describe('Mon profil', function() {
                test.it('displays correct address', function(done) {
                    return myUser.driver.wait(until.elementLocated(By.css('.sel-mon_profil_link')))
                        .then(function() {
                            return myUser.driver.findElement(By.css('.sel-mon_profil_link')).click()
                        }).then(function() {
                            return myUser.driver.wait(until.elementLocated(By.css('.sel-address_1')));
                        }).then(function() {
                            return getFullAddress(69002);
                        }).then(function(address) {
                            let trimedAddress = [];
                            address.forEach(function(fragment) {
                                trimedAddress.push(fragment.trim());
                            });
                            address = trimedAddress;

                            console.dir(address);
                            myUser.driver.findElement(By.css('.sel-address_1')).getText()
                                .should.eventually.equal(address[0]);
                            myUser.driver.findElement(By.css('.sel-address_2')).getText()
                                .should.eventually.equal(address[1]);
                            myUser.driver.findElement(By.css('.sel-address_3')).getText()
                                .should.eventually.equal(address[2]);
                            myUser.driver.findElement(By.css('.sel-postcode_city')).getText()
                                .should.eventually.equal(address[3] + ' ' + address[4]);
                            return address;
                        }).then(function(address) {
                            done();
                        }).catch(function(err) {
                            done(err);
                        })
                });

                test.it('displays correct name', function(done) {
                    return myUser.driver.findElement(By.css('.sel-mon_profil_link')).click()
                        .then(function() {
                            return myUser.driver.wait(until.elementLocated(By.css('.sel-nom_etude')));
                        }).then(function() {
                            return getEtudeName(69002);
                        }).then(function(name) {
                            name = name[0].trim();
                            if(debug)
                                console.dir(name);
                            myUser.driver.findElement(By.css('.sel-nom_etude')).getText()
                                .should.eventually.equal(name);
                            return name;
                        }).then(function(name) {
                            done();
                        }).catch(function(err) {
                            done(err);
                        })
                });

                test.it('displays correct email address (3-lines-full)', function(done) {
                    return myUser.driver.findElement(By.css('.sel-mon_profil_link')).click()
                        .then(function() {
                            return myUser.driver.wait(until.elementLocated(By.css('#sel-compte-mail')));
                        }).then(function() {
                            return getEtudeEmail(69002);
                        }).then(function(email) {
                            email = email[0].trim();
                            if(debug)
                                console.dir(email);
                            myUser.driver.findElement(By.css('#sel-compte-mail')).getText()
                                .should.eventually.equal(email);
                            return email;
                        }).then(function(email) {
                            done();
                        }).catch(function(err) {
                            done(err);
                        })
                });

                test.it.skip('should return status code 200 if document is available', function (done) {
                    const http = require('selenium-webdriver/http'),
                        HttpClient = http.HttpClient,
                        Request = http.Request;

                    const url = 'https://cridon.jetpulp.work/documents/download/166053/';
                    const method = 'HEAD';
                    let client = new HttpClient(url);
                    let httpRequest = new Request(method, url);

                    return client.send(httpRequest)
                        .then(function(response) {
                            if(debug)
                                console.log('HTTP Status code: ' + response.status);
                            return response.status;
                        }, function(err) {
                            return Promise.reject(err);
                        }).then(function(status) {
                            expect(status.toString()).to.equal('200', 'Document expected to be availabe, returned status code ' + status + ' instead');
                            done();
                        }).catch(function(err) {
                            done(err);
                        });

                });

                test.it('should properly download a document', function (done) {
                    // MIME type test files at:
                    // http://www.yolinux.com/TUTORIALS/LinuxTutorialMimeTypesAndApplications.html
                    const tika = require('tika');

                    const url = 'https://cridon.jetpulp.work/documents/download/166053/'

                    tika.type(url, function(err, type) {
                        tika.language(url, function(err, lang) {
                            Promise.resolve(type).should.eventually.equal('application/pdf')
                                .then(function() {
                                    done();
                                }).catch(function(err) {
                                    done(err);
                            })
                        });
                    });
                });

                test.it('uses mailhog (Test to be deleted)', function(done) {
                    const mailhog = require('mailhog')({
                        apiURL: 'http://127.0.0.1:8025/api/v2'
                    });

                    mailhog.search('renaud.amsellem@jetpulp.fr')
                        .then(function(res) {
                            if(res.count === 1)
                                done();
                            else
                                return Promise.reject(res.count);
                        }).catch(function(err) {
                            done(err);
                    });
                });

                test.it('uses nodemailer', function(done) {
                    const nodemailer = require('nodemailer');

                    var transporter = nodemailer.createTransport({
                        service: 'Gmail',
                        auth: {
                            user: 'cridon.lyon.test@gmail.com',
                            pass: 'ilovejetpulp'
                        }
                    });

                    var mailOptions = {
                        from: 'NoReply <cridon.lyon.test@gmail.com>',
                        to: 'adriengarcia94@gmail.com',
                        subject: 'Demande de pré-inscription enregistrée ✔',
                        text: 'Votre demande de pré-inscription à une session de formation dispensée par le CRIDON Lyon a bien été enregistrée.',
                        html: '<b>Hello world ✔</b>'
                    };

                    transporter.sendMail(mailOptions, function (error, info) {
                        if (error) {
                            console.error(error);
                            done(error);
                        } else {
                            console.log('Message sent:', info.response);
                            done();
                        }
                    });
                });

                test.it('allows to update Etude', function(done) {
                    // return myUser.openEtudeUpdate()
                    //     }).then(function() {
                    //         return myUser.updateEtude({
                    //             address_1: '1 rue du Deux Mars 1904',
                    //             address_2: '',
                    //             address_3: '',
                    //             postcode: '69002',
                    //             city: 'Lyon',
                    //             email: 'sample@notaires.fr',
                    //             telephone: '000',
                    //             fax: '111'
                    //         });
                    //     }).then(function() {
                    //         done();
                    //     }).catch(function(err) {
                    //         done(err);
                    // })

                    let that = this;

                    let form, submit;
                    // let addr1, addr2, addr3, postcode, city, email, telephone, fax;

                    myUser.driver.get(page_index.DashboardPage.url)
                        .then(function() {
                            // need to be on dashboard here
                            return myUser.driver.findElement(By.css('#sel-compte-profil-button')).click();
                        }).then(function() {
                            return myUser.driver.wait(until.elementLocated(By.css('.update.update-etude.js-account-profil-office-modify')));
                        }).then(function() {
                            return myUser.driver.findElement(By.css('.update.update-etude.js-account-profil-office-modify')).click();
                        }).then(function() {
                            return myUser.driver.wait(until.elementIsVisible(myUser.driver.findElement(By.css('#layer-update-etude'))));
                        }).then(function() {
                            return Promise.all([
                                myUser.driver.findElement(By.css('form.js-account-profil-office-modify-form')),
                                myUser.driver.findElement(By.css('form.js-account-profil-office-modify-form input[type="submit"]'))
                            ]).then(function(elements) {
                                form = elements[0];
                                submit = elements[1];
                                return submit.click();
                            });
                        }).then(function() {
                            // page refreshing, declared form becoming stale
                            return myUser.waitStalenessOf(form);
                        }).then(function() {
                            // success message should be visible
                            return myUser.driver.wait(until.elementIsVisible(myUser.driver.findElement(By.css('#mon-profil .message-erreur'))));
                        }).then(function() {
                            return getFullAddress(69002);
                        }).then(function(name) {
                            let s = '';
                            name.forEach(function(frag) {
                                s = s + ' ' + frag;
                                s = s.trim();
                            });
                            console.log(s);
                            return s;
                        }).then(function() {
                            done();
                        }).catch(function(err) {
                            done(err);
                    });
                });

            });

        });

    });

});


let executeQuery = function(query) {
    let RESULT = [];

    return sequelize.query(
        query,
        {
            type: Sequelize.QueryTypes.SELECT,
            logging: false
        }
    ).then(function(rows) {
        rows.forEach(function(table) {
            Object.keys(table).forEach(function(key) {
                RESULT.push(table[key]);
            });
        })
    }).then(function() {
        // console.dir(RESULT)
        return RESULT;
    });
};

let getFullAddress = function(crpcen) {
    let q = 'SELECT adress_1, adress_2, adress_3, cp, city FROM cri_etude WHERE crpcen=' + crpcen + ';';

    return executeQuery(q);
};

let getEtudeName = function(crpcen) {
    let q = 'SELECT office_name FROM cri_etude WHERE crpcen=' + crpcen + ';';

    return executeQuery(q);
};

let getEtudeEmail = function(crpcen) {
    let q = 'SELECT office_email_adress_1 FROM cri_etude WHERE crpcen=' + crpcen + ';';

    return executeQuery(q);
};

let selectFromWhere = function(attribute, table, condition) {
    let q = 'SELECT ' + attribute + ' FROM ' + table + ' WHERE ' + condition + ';';

    return executeQuery(q);
};

let lintSemicolon = function(query) {
    query.trim();
    if(query.charAt(query.length - 1) != ';')
        return query + ';';
};