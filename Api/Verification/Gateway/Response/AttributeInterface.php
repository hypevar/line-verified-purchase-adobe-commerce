<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Verification\Gateway\Response;

/**
 * Holds all fields from Gateway's Verification Process response
 */
interface AttributeInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const FIELD_VALIDATED = 'Validado';
    public const FIELD_MESSAGE = 'Mensaje';
    public const FIELD_VERIFIED_PURCHASE_ID = 'CompraVerificadaID';
    public const FIELD_TRANSACTION_IDENTIFIER = 'IdentificadorTX';
    public const FIELD_BUYER_IDENTIFIER = 'IdentificadorComprador';
    /**#@-*/
}
