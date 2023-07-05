<?php

namespace Dev\Donation\Model\Quote\Address\Total;

/**
 * Class Donation
 * @package Dev\Donation\Model\Quote\Address\Total
 */
class Donation extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Constant for the donation key
     */
    const DONATION_KEY = 'donation_amount';

    /**
     * Collect total amount for the donation.
     *
     * @param \Magento\Quote\Model\Quote $quote - Quote object
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment - Shipping assignment object
     * @param \Magento\Quote\Model\Quote\Address\Total $total - Total object
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        // Add the donation amount to the total
        $total->addTotalAmount(self::DONATION_KEY, $quote->getDonationAmount());
        $total->addBaseTotalAmount(self::DONATION_KEY, $quote->getDonationAmount());

        return $this;
    }

    /**
     * Fetch the donation amount.
     *
     * @param \Magento\Quote\Model\Quote $quote - Quote object
     * @param \Magento\Quote\Model\Quote\Address\Total $total - Total object
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code' => $this->getCode(),
            'title' => __('Donation amount'),
            'value' => $quote->getDonationAmount()
        ];
    }
}
