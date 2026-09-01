<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Gateway\Api;

use Line\VerifiedPurchase\Api\ResponseInterface;
use Magento\Framework\DataObject;

class AuthorizationResponse extends DataObject implements ResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getStatus(): bool
    {
        return $this->_getData(self::FIELD_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(bool $value): self
    {
        return $this->setData(self::FIELD_STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVerifiedPurchaseId(): string
    {
        return $this->_getData(self::FIELD_VERIFIED_PURCHASE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVerifiedPurchaseId(string $value): self
    {
        return $this->setData(self::FIELD_VERIFIED_PURCHASE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVerificationChannel(): string
    {
        return $this->_getData(self::FIELD_VERIFICATION_CHANNEL);
    }

    /**
     * @inheritDoc
     */
    public function setVerificationChannel(string $value): self
    {
        return $this->setData(self::FIELD_VERIFICATION_CHANNEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVerificationMode(): string
    {
        return $this->_getData(self::FIELD_VERIFICATION_MODE);
    }

    /**
     * @inheritDoc
     */
    public function setVerificationMode(string $value): self
    {
        return $this->setData(self::FIELD_VERIFICATION_MODE, $value);
    }
}
