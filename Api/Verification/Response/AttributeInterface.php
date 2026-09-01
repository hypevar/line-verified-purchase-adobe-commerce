<?php

/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Verification\Response;

/**
 * Holds all response fields from a Verification Process object
 */
interface AttributeInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const FIELD_VALIDATED = 'validated';
    public const FIELD_MESSAGE = 'message';
    public const FIELD_VERIFIED_PURCHASE_ID = 'verified_purchase_id';
    public const FIELD_TRANSACTION_IDENTIFIER = 'transaction_id';
    public const FIELD_BUYER_IDENTIFIER = 'buyer_identifier';
    /**#@-*/
}
