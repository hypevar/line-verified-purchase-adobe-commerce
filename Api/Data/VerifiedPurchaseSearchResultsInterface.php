<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

namespace Line\VerifiedPurchase\Api\Data;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for verification process search results
 */
interface VerifiedPurchaseSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get verification processes list
     *
     * @return VerifyPurchaseInterface[]
     */
    public function getItems();

    /**
     * Set verification processes list
     *
     * @param VerifyPurchaseInterface[] $items
     *
     * @return self
     */
    public function setItems(array $items);
}
