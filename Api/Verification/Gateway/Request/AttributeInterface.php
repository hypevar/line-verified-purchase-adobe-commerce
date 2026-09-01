<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Verification\Gateway\Request;

/**
 * Holds all fields from Gateway's Verification Process request
 */
interface AttributeInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const FIELD_CODE = 'Codigo';
    public const FIELD_VERIFIED_PURCHASE_ID = 'CompraVerificadaID';
    public const FIELD_MAX_TRIES = 'CantidadMaximaIntentos';
    public const FIELD_IP_ADDRESS = 'IPAddress';
    /**#@-*/
}
