<?php

namespace Dev\Donation\Api;

/**
 * Interface DonationManagementInterface
 * @api
 */
interface DonationManagementInterface
{
    /**
     * @param string $quoteId
     * @param string $amount
     * @return bool
     */
    public function set($quoteId, $amount);
}
