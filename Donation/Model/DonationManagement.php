<?php

namespace Dev\Donation\Model;

class DonationManagement implements \Dev\Donation\Api\DonationManagementInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * DonationManagement constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory    $quoteIdMaskFactory,
        \Psr\Log\LoggerInterface                   $logger,
        \Magento\Customer\Model\Session            $customerSession
    )
    {
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
    }

    /**
     * Set donation amount for a quote.
     *
     * @param string $quoteId
     * @param string $amount
     * @return bool
     */
    public function set($quoteId, $amount)
    {
        $amount = (float)$amount;

        //Check customer logged in
        if ($this->customerSession->isLoggedIn()) {
            $quote = $this->cartRepository->getActive($quoteId);
        } else {
            $quoteId = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id')->getQuoteId();
            $quote = $this->cartRepository->getActive($quoteId);
        }
        $cartTotal = (float)$quote->getGrandTotal();
        $cartDonationAmount = (float)$quote->getDonationAmount();

        //Subtract the existing donation amount from the cart total
        if ($cartDonationAmount !== 0) {
            $cartTotal = $cartTotal - $cartDonationAmount;
        }

        // Calculate the new total with the added donation amount
        $newTotal = $cartTotal + $amount;

        // Get address totals data based on whether the quote is virtual or not
        if ($quote->isVirtual()) {
            $addressTotalsData = $quote->getBillingAddress()->getData();
        } else {
            $addressTotalsData = $quote->getShippingAddress()->getData();
        }

        // Update the grand total in the address totals data
        $addressTotalsData['grand_total'] = $newTotal;
        // Set the updated address totals data in the shipping address object
        $quote->getShippingAddress()->setData($addressTotalsData);

        try {
            $quote->setDonationAmount($amount);
            $quote->collectTotals();
            $quote->save();
        } catch (\Exception $exception) {
            $this->logger->addError($exception->getMessage());
            return false;
        }

        return true;
    }
}
