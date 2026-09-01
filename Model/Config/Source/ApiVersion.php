<?php
/**
 * Copyright © 2023 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Config\Source;

class ApiVersion
{
    /**
     * Returns an array with available options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'v1', 'label' => __('Version 1')],
            ['value' => 'v2', 'label' => __('Version 2')]
        ];
    }
}
