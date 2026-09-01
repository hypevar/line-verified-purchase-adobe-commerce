<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Request;

/**
 * Request fields during the intiial Authorization Process
 */
interface AttributeInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const FIELD_VERIFIED_PURCHASES = 'CompraVerificada';
    /**
     * @see Attribute\ValidationModeInterface
     */
    public const FIELD_VERIFICATION_MODE = 'ModoVerificacion';
    /**
     * @see Attribute\TimeUnitInterface
     */
    public const FIELD_TIME_UNIT = 'UnidadTiempo';
    public const FIELD_TIME_AMOUNT = 'CantidadTiempo';
    public const FIELD_CUSTOMER_IDENTIFIER = 'IdentificadorComprador';
    public const FIELD_CREDIT_CARD_SUMMARY_DESCRIPTION = 'NombreFantasia';
    /**#@-*/
}
