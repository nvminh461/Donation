<?php

namespace Dev\Donation\Plugin\Order;

class TotalsAddDonation
{
    /**
     * @var \Magento\Framework\DataObject
     */
    protected $dataObject;

    /**
     * TotalsAddDonation constructor.
     * @param \Magento\Framework\DataObject $dataObject - Data object to store total data
     */
    public function __construct(\Magento\Framework\DataObject $dataObject)
    {
        $this->dataObject = $dataObject;
    }

    /**
     * Add donation amount to the order totals block.
     *
     * @param \Magento\Sales\Block\Order\Totals\Interceptor $totals - Order totals block
     */
    public function beforeGetTotals($totals)
    {
        // Check if the 'donation_amount' total is already added
        if (!$totals->getTotal('donation_amount')) {
            // Get the order object from the totals block
            $order = $totals->getOrder();

            // Create an array with the total data
            $totalData = [
                'code' => 'donation_amount',
                'strong' => true,
                'value' => $order->getDonationAmount() != null ?  $order->getDonationAmount():0,
                'label' => __('Donation amount')
            ];

            // Set the total data to the data object
            $this->dataObject->setData($totalData);

            // Add the total data to the order totals block
            $totals->addTotal($this->dataObject);
        }
    }
}
