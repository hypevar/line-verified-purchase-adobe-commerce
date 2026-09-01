<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class VerifyPurchase extends AbstractDb
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_init('verified_purchase_customer_order', 'entity_id');
    }
}
