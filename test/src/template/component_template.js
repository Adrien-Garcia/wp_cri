// function WebComponent (webdriver, selector) {
//     this.driver = webdriver;
//
//     if(typeof this.element != WebElement) {
//         let that = this;
//         webdriver.findElement(By.css(this.element))
//             .then(function (webElem) {
//                 that.element = webElem;
//                 return that;
//             });
//     }
// }
//
// // Prototype linkage
// WebComponent.prototype = Object.create(WebElement.prototype);
// WebComponent.prototype.constructor = WebComponent;
//
// WebComponent.prototype.init = function() {
//     for(let locator in this.locators) {
//         let elements = webelement.findElements(this.locators[locator]);
//         if (elements.length === 0) {
//             throw new Error();
//         } else if (elements.length === 1) {
//             Promise.resolve();
//         } else {
//             Promise.all([]);
//         }
//     }
//
//     for (let fn in this.helpers) {
//         Object.getPrototypeOf(this)[fn] = this.helpers[fn];
//     }
// };
//
// module.exports = WebComponent;
//
// /**********************/
//
// "use strict";
// /************************** COMPONENT DEFINITION ******************************/
// const componentData = {
//     css: '#CSS.selector',
//     locators: {
//         // DOM_element_function_and_tag_name: 'css.selector'
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
// /************************* IMPORTS AND SETUP **********************************/
// Standard modules
// const By = require('selenium-webdriver').By,
//     until = require('selenium-webdriver').until;
// const util = require('util');
// // Other variables
// const debug = 0;
// /*************************** CONSTRUCTOR **************************************/
// /**Creates an instance of <<MyComponent>>.
//  * @constructor
//  * @param webdriver {WebDriver} The Selenium webdriver currently running.
//  * @param selector {string} A CSS selector for this component.
//  */
// function <<MyComponent>> (webdriver, selector) {
//     // WebComponent abstract constructor
//     WebComponent.call(this, webdriver, selector);
//
//     // Attaching data to <<MyComponent>>
//     Object.assign(this, JSON.parse(JSON.stringify(pageData)));
// } // End of <<MyComponent>> constructor
// /************************** PROTOTYPE CHAIN ***********************************/
// <<MyComponent>>.prototype = Object.create(WebComponent.prototype);
// <<MyComponent>>.prototype.constructor = <<MyComponent>>;
// /************************* SPECIFIC METHODS ***********************************/
// <<MyComponent>>.prototype.fn = function() {
//     if(debug)
//         console.log('This is a sample function');
//     return this;
// };
// /*************************** MODULE EXPORT ************************************/
// /**
//  * @module <<MyComponent>>
//  */
// module.exports = <<MyComponent>>;
