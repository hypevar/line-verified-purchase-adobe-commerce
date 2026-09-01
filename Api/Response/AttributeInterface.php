<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Response;

/**
 * Response fields during the intial Authorization Process
 */
interface AttributeInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const FIELD_VERIFIED_PURCHASE = 'CompraVerificada';
    public const FIELD_STATUS = 'Estado';
    public const FIELD_VERIFIED_PURCHASE_ID = 'CompraVerificadaID';
    public const FIELD_VERIFICATION_CHANNEL = 'CanalVerificacion';
    public const FIELD_VERIFICATION_MODE = 'ModoVerificacion';
    /**#@-*/
}
