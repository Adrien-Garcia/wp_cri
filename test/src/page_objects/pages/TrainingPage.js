"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path:'/formations/vente-dimmeuble-pratique-notariale-des-avant-contrats-impact-de-la-reforme-du-droit-des-obligations', // <!!> Temporary
    title:'Formations | ', // <!!> Temporary
    components: {
        // Default
	},
    locators: {
		product_options_fieldset: '.sel-product-options_product',
		required_entries: '.sel-product-options_product .required-entry',
		extra_option_select: '#select_9', // <!!!> Product-specific

		quantity_input: '.sel-quantity-input_product',
		add_cart_button: '.sel-add-cart-button_product',
        add_wishlist_link: '.sel-add-wishlist-link_product',
        products_comparison_link: '.sel-products-comparison-link_product'
        // TODO
        // purchase_store: {
        //     add_cart_button: ''
        // },
        // update_cart: {
        //     update_qty_button: ''
        // }
    }
};


// NOTE: exports nativly integrate the functionality
module.exports.index = Object.assign({},
    {
        ProductPage: {
            path: pageData.path,
            title: pageData.title
        }
    }
);
/******************************************************************************/
/***** Imports and setup *****/
// Standard modules
const By = require('selenium-webdriver').By,
        until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
const WebPage = require('./WebPage');
const debug = 0;
/**
 * Creates an instance of ProductPage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 * @see WebPage
 */
function ProductPage (webdriver, isAuthenticated) {
	// WebPage inherited constructor
	WebPage.call(this, webdriver, isAuthenticated);
	// Importing page-specific CSS locators
	Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}// <== End of ProductPage constructor

// Prototype linkage
ProductPage.prototype = Object.create(WebPage.prototype);
ProductPage.prototype.constructor = ProductPage;
// METHODS
/**
 * I should really write some description for this method
 * @instance
 * @param quantity {number} The quantity of items to add to the cart.
 * @returns {Thenable<ProductPage> | Thenable<undefined>}
 */
ProductPage.prototype.addProduct = function(qty) {
	let that = this;
	if(debug)
		console.log('extra_option_select is: ' + util.inspect(this.extra_option_select, true, 0, true));
	this.getRandomOption(this.extra_option_select)
    .then(function(validOption) {
	  return that.setOptionByValue(that.extra_option_select, validOption);
    });
	this.quantity_input.clear();
	this.quantity_input.sendKeys(qty);
	this.add_cart_button.click();
	this.driver
	.wait(until.elementLocated(By.css('li.success-msg')), 10000);
	return this.driver
	.getCurrentUrl()
	.then(function(url) {
		if(url !== require('../../page_context').page_index.CartPage.url) {
			return that.driver
			.findElement(By.css('#j2t-checkout-link'))
			.click();
		}
		return that;
	});
};

// /** @module ~/src/page_objects/pages/ProductPage */
module.exports.class = ProductPage;
