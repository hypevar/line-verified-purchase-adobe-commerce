<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Magento\Customer\Model\Session;

/**
 * Interacts with the latest accessed Verification Identifier value
 */
class LastVerificationIdentifierAction
{
    /**
     * @var string
     */
    private const SESSION_KEY = 'last_verification_identifier';

    /**
     * @var Session
     */
    private Session $session;

    /**
     * Class constructor
     *
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Returns last identifier saved into the Customer registry
     *
     * @return string|null
     */
    public function get()
    {
        return $this->session->getData(self::SESSION_KEY);
    }

    /**
     * Returns last identifier saved into the Customer registry
     *
     * @param string $identifier
     *
     * @return self
     */
    public function set(string $identifier)
    {
        $this->session->setData(self::SESSION_KEY, $identifier);
        return $this;
    }
}
