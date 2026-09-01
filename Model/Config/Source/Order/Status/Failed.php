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
class Failed extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [
        OrderStatusInterface::STATE_NEW,
        OrderStatusInterface::STATE_PENDING_PAYMENT,
        OrderStatusInterface::STATE_PAYMENT_REVIEW
    ];
}
