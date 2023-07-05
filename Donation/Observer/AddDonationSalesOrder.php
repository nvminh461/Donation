<?php

namespace Dev\Donation\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddDonationSalesOrder implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * AddDonationSalesOrder constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository - Interface for managing quote entities
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * Add donation amount to the sales order.
     *
     * @param \Magento\Framework\Event\Observer $observer - Observer object
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Get the order object from the observer
        $order = $observer->getData('order');

        // Get the quote ID from the order
        $quoteId = $order->getQuoteId();

        // Load the quote using the quote repository
        $quote = $this->cartRepository->getActive($quoteId);

        // Set the donation amount from the quote to the order
        $order->setDonationAmount($quote->getDonationAmount());
    }
}
