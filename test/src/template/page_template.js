// "use strict";
// /***************************** PAGE DEFINITION ********************************/
// const pageData = {
//     path: '/path/from/host/', // host = process.env.BASE_HOST_URL
//     title: 'Page title',
//     locators: {
//         // DOM_element_function_and_tag_name: 'css.selector'
//         //
//         // or
//         //
//         // Empty
//     },
//     components: {
//         // name: 'ComponentClass'
//         //
//         // or
//         //
//         // Empty
//     },
//     data: {
//         // Custom data (credentials, expected messages, form values, etc.)
//         //
//         // or
//         //
//         // Empty
//     }
// };
// /******************************* INDEX EXPORT *********************************/
// module.exports.index = Object.assign({},
//     {
//         <<MyPage>>: {
//             path: pageData.path,
//             title: pageData.title
//             // Full URL is defined into ~/test/src/page_context.js
//         }
//     }
// );
// /************************* IMPORTS AND SETUP **********************************/
// // Standard modules
// // NOTE: No assertion libraries should be found down below!
// const By = require('selenium-webdriver').By,
//     until = require('selenium-webdriver').until;
// const util = require('util');
// // Project modules
// // NOTE: Keep in mind the project's architecture to avoid fatal interdependence
// const WebPage = require('./WebPage'); // abstract model for a web page
// // Custom errors/exceptions libraries
// const CustomExceptions = require('../../custom_exceptions');
// // Other variables
// const debug = 0;
// /*************************** CONSTRUCTOR **************************************/
// /**Creates an instance of <<MyPage>>.
//  * @constructor
//  * @param webdriver {WebDriver} The Selenium webdriver currently running.
//  * @param isAuthenticated {boolean} True iff user is authenticated on this page.
//  */
// function <<MyPage>> (webdriver, isAuthenticated) {
//     // WebPage abstract constructor
//     WebPage.call(this, webdriver, isAuthenticated);
//
//     // Attaching data to <<MyPage>>
//     Object.assign(this, JSON.parse(JSON.stringify(pageData)));
// } // End of <<MyPage>> constructor
// /************************** PROTOTYPE CHAIN ***********************************/
// <<MyPage>>.prototype = Object.create(WebPage.prototype);
// <<MyPage>>.prototype.constructor = <<MyPage>>;
// /************************* SPECIFIC METHODS ***********************************/
// <<MyPage>>.prototype.fn = function() {
// 	if(debug)
//         console.log('This is a sample function');
// 	return this;
// };
// /*************************** CLASS EXPORT *************************************/
// module.exports.class = <<MyPage>>;
