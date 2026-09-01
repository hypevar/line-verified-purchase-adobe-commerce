<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Config\Source;

use Line\VerifiedPurchase\Api\Request\Attribute\ValidationModeInterface;

class VerificationModes
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
            ['value' => ValidationModeInterface::IMMEDIATE_MODE, 'label' => __('Immediate Mode')]
            // Long Term not implemented
            // ['value' => ValidationModeInterface::LONGTERM_MODE, 'label' => __('Long Term Mode')]
        ];
    }
}
