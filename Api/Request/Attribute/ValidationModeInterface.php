<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Request\Attribute;

interface ValidationModeInterface
{
    /**
     * Immediate Mode validation mode
     *
     * Generates a random consume against the Customer's CC, between 10.0 and 19.99.
     * It will appear as the Credit Card Summary Description value.
     *
     * @access public
     * @var string
     */
    public const IMMEDIATE_MODE = 'INMEDIATO';

    /**
     * Long Term validation mode
     *
     * @access public
     * @var string
     */
    public const LONGTERM_MODE = 'LARGOPLAZO';
}
