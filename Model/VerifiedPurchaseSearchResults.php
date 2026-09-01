<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Line\VerifiedPurchase\Api\Data\VerifiedPurchaseSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object for search results
 */
class VerifiedPurchaseSearchResults extends SearchResults implements VerifiedPurchaseSearchResultsInterface
{
}
