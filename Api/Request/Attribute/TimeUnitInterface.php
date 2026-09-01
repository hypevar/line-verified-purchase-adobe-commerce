<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Request\Attribute;

interface TimeUnitInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const UNIT_DAY = 'DIA';
    public const UNIT_HOUR = 'HORA';
    public const UNIT_MINUTE = 'MINUTO';
    /**#@-*/
}
