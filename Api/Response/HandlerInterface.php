<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Response;

use Magento\Payment\Gateway\Response\HandlerInterface as PaymentHandlerInterface;

/**
 * @api
 * @since 0.1.0
 */
interface HandlerInterface extends PaymentHandlerInterface
{
}
