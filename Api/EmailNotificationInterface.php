<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;

/**
 * Handles all notification to the buyer
 */
interface EmailNotificationInterface
{
    /**
     * Send a Pending email notification to the buyer
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     */
    public function notifyPending(VerifyPurchaseInterface $verification);

    /**
     * Send a Complete email notification to the buyer
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     */
    public function notifyComplete(VerifyPurchaseInterface $verification);

    /**
     * Send a Failed email notification to the buyer
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     */
    public function notifyFailed(VerifyPurchaseInterface $verification);
}
