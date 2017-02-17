"use strict";
/******************************* INDEX ****************************************/
const pageData = {
    path: '/ordinateur-portable/pc-portable', // <!!> Sample
    title: 'PC Portable - Ordinateur portable', // <!!> Sample
    components: {
        // Default
    },
    locators: {
        // Side panel for filter selection
        products_filter_links: '.sel-products-filter-link_catalogue',

        // last_viewed_links: '#recently-viewed-items li.item .product-name a',
        // last_wishlist_products_links: '#wishlist-sidebar li.item .product-name a',
        // last_wishlist_add_header_cart_links: '#wishlist-sidebar li.item .link-cart',

        paypal_info_link: '.paypal-logo a[title="Options additionnelles"]'
    },
    data: {
		locators_status: {
			empty: {
				empty_message: 'p.note-msg'
			},
			default: {
                // Top toolbar
                top_sort_by_select: '.sel-sort-by-select_catalogue',
                top_sort_switcher_link: '.sel-sort-switcher-link_catalogue',
                // top_display_mode_links: '.category-products > .toolbar .view-mode a.list',
                // top_page_number_info: '.category-products > .toolbar .pager p.amount',
                top_page_number_select: '.category-products > .toolbar .pager select',

                // Products
                item_image_links: '.sel-item-product-image-link_catalogue',
                item_names_links: '.sel-item-product-name-link_catalogue',
                add_wishlist_links: '.sel-add-wishlist-link_catalogue',
                add_comparison_links: '.sel-add-comparison-link_catalogue',
                add_item_cart_links: '.sel-add-item-cart-button_catalogue',

                // Bottom toolbar
                // bottom_sort_by_select: '.toolbar-bottom .sort-by select',
                // bottom_sort_switcher_link: '.toolbar-bottom .sort-by .sort-by-switcher',
                // bottom_display_mode_links: '.toolbar-bottom .view-mode a.list',
                // bottom_page_number_info: '.toolbar-bottom .pager p.amount',
                // bottom_page_number_select: '.toolbar-bottom .pager select'
		    }
		}
	}
};



module.exports.index = Object.assign({},
    {
        CataloguePage: {
            path: pageData.path,
            title: pageData.title
            // Page's URL is defined elsewhere from host's base URL (page_context.js)
        }
    }
);
/************************* IMPORTS AND SETUP **********************************/
// Standard modules
// NOTE: NO ASSERTION LIBRARY HERE!
const By = require('selenium-webdriver').By,
        until = require('selenium-webdriver').until;
const util = require('util');
// Project modules
// NOTE: Keep in mind the project's architecture to avoid fatal interdependence
const WebPage = require('./WebPage');
// Other variables
const debug = 0;
/*************************** CONSTRUCTOR **************************************/
/**Creates an instance of CataloguePage.
 * @constructor
 * @param webdriver {WebDriver} The Selenium webdriver currently running.
 * @param isAuthenticated {boolean} True iff user is authenticated on this page.
 */
function CataloguePage (webdriver, isAuthenticated) {
	if (!(this instanceof CataloguePage))
    	throw new SyntaxError(
            "CataloguePage constructor needs to be called with the 'new' keyword."
        );
    // Webpage abstract constructor
    WebPage.call(this, webdriver, isAuthenticated);
    // Basic information for CataloguePage
    Object.assign(this, JSON.parse(JSON.stringify(pageData)));
}
/************************** PROTOTYPE CHAIN ***********************************/
// Prototype linkage with abstract WebPage
CataloguePage.prototype = Object.create(WebPage.prototype);
// Referencing the correct constructor
CataloguePage.prototype.constructor = CataloguePage;
/************************* SPECIFIC METHODS ***********************************/
/**Select a product by its name.
 * @returns unknown
 */
CataloguePage.prototype.selectProductByName = function(name) {
    for(let itemName in this.item_names_links) {
        itemName.getText()
        .then(function(linkText) {
            return linkText === name;
        }).then(function(requestedElem) {
            if(requestedElem) {
                return itemName.click();
            } else {
                return 1;
            }
        });
    }
};
/**Select a product by its index into the currently displayed selection.
 * @returns unknown
 */
CataloguePage.prototype.selectProductByIndex = function(index) {
	return this; // 'this' corresponds to the instance calling 'myFunc'
};
/*************************** CLASS EXPORT *************************************/
// /** @exports CataloguePage */
module.exports.class = CataloguePage;
