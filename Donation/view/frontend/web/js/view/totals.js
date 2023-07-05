define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'Magento_Catalog/js/price-utils'
], function (
    ko,
    Component,
    quote,
    totals,
    priceUtils
) {
    'use strict';

    // Component definition
    return Component.extend({
        defaults: {
            template: 'Dev_Donation/totals'
        },

        /**
         * Check if donation has value different 0
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            var displayed;
            // Check if the donation amount is not equal to 0
            if (totals.getSegment('donation_amount').value !== 0) {
                displayed = true;
            } else {
                displayed = false;
            }
            return displayed;
        },

        getValue: function () {
            // Get the donation amount from the totals
            var price = totals.getSegment('donation_amount').value;
            // Format the price using the quote's price format
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    });
});
