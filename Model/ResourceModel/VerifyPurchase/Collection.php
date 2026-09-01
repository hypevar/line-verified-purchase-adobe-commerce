<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\ResourceModel\VerifyPurchase;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Verify Purchase Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * @var string
     */
    protected $_eventPrefix = 'verified_purchase_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'verified_purchase_collection';

    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Line\VerifiedPurchase\Model\VerifyPurchase::class,
            \Line\VerifiedPurchase\Model\ResourceModel\VerifyPurchase::class
        );
    }
}
