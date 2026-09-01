<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Config\Source;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;

class Modes
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
            ['value' => ConfigInterface::MODE_SANDBOX, 'label' => __('Sandbox')],
            ['value' => ConfigInterface::MODE_PRODUCTION, 'label' => __('Production')]
        ];
    }
}
