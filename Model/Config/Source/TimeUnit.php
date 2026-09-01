<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Config\Source;

use Line\VerifiedPurchase\Api\Request\Attribute\TimeUnitInterface;

class TimeUnit
{
    /**
     * Returns an array with available options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => TimeUnitInterface::UNIT_DAY, 'label' => __('Day')],
            ['value' => TimeUnitInterface::UNIT_HOUR, 'label' => __('Hour')],
            ['value' => TimeUnitInterface::UNIT_MINUTE, 'label' => __('Minute')],
        ];
    }
}
