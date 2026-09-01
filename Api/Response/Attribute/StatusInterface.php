<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Response\Attribute;

/**
 * Gateway's possible values for status field
 */
interface StatusInterface
{
    public const STATUS_OK = 'OK';
    public const STATUS_ERROR = 'ERROR';
}
