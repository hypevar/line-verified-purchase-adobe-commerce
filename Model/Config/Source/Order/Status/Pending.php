<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Config\Source\Order\Status;

use Line\Payment\Api\Data\OrderStatusInterface;

/**
 * Returns all new and pending states that an Order can be set
 */
class Pending extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [
        OrderStatusInterface::STATE_PROCESSING
    ];
}
