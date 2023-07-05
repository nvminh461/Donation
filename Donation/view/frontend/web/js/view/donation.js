define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, ko, Component, quote, storage, urlBuilder, customer, methodConverter, paymentService, globalMessageList, translate) {
    'use strict';
    var isDonated = ko.observable(false);

    // Component definition
    return Component.extend({
        defaults: {
            template: 'Dev_Donation/donation'
        },
        chosenAmount: ko.observable(''),

        /**
         * Check if donation has been made
         */
        isDonated: isDonated,

        changeDonationAmount: function () {
            // Check if the chosen amount is valid
            if (this.validate()) {
                if (!this.chosenAmount()) {
                    return;
                }

                // Create the service URL to save the donation amount
                var serviceUrl = urlBuilder.createUrl('/donation/:cartId/amount/:donationAmount', {
                    cartId: quote.getQuoteId(),
                    donationAmount: this.chosenAmount()
                });
                var donation = this;
                isDonated(true);

                // Send the request to save the donation amount
                return storage.put(
                    serviceUrl, false
                ).done(
                    function (response) {
                        if (response) {
                            donation.updateTotals();
                        }
                    }
                ).fail(
                    function (response) {
                        // Display an error message if the request fails
                        globalMessageList.addErrorMessage({
                            message: translate('An error occurred on the server. Please try to place the order again.')
                        });
                    }
                );
            }
        },

        updateTotals: function () {
            var serviceUrl = '';
            var deferred = deferred || $.Deferred();

            // Create the service URL to update the totals
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/payment-information', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            // Send the request to update the totals
            return storage.get(
                serviceUrl, false
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    deferred.resolve();
                }
            ).fail(
                function (response) {
                    // Display an error message if the request fails
                    globalMessageList.addErrorMessage({
                        message: translate('An error occurred on the server. Please try to place the order again.')
                    });
                }
            );
        },

        /**
         * Cancel donation
         */
        cancel: function () {
            // Create the service URL to cancel the donation
            var serviceUrl = urlBuilder.createUrl('/donation/:cartId/amount/:donationAmount', {
                cartId: quote.getQuoteId(),
                donationAmount: this.chosenAmount(0)
            });
            var donation = this;
            isDonated(false);

            // Send the request to cancel the donation
            return storage.put(
                serviceUrl, false
            ).done(
                function (response) {
                    if (response) {
                        donation.updateTotals();
                    }
                }
            ).fail(
                function (response) {
                    // Display an error message if the request fails
                    globalMessageList.addErrorMessage({
                        message: translate('An error occurred on the server. Please try to place the order again.')
                    });
                }
            );
        },

        /**
         * Donation form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#donation-form';
            // Perform validation on the donation form
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
