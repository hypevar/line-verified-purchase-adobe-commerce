<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Gateway\Api;

use Line\VerifiedPurchase\Api\Verification\ResponseInterface;
use Magento\Framework\DataObject;

/**
 * Service response from Customer trying to validate a code
 */
class Response extends DataObject implements ResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getValidated(): bool
    {
        return $this->_getData(self::FIELD_VALIDATED);
    }

    /**
     * @inheritDoc
     */
    public function setValidated(bool $value): self
    {
        return $this->setData(self::FIELD_VALIDATED, $value);
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
    public function getMessage(): ?string
    {
        return $this->_getData(self::FIELD_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $value): self
    {
        return $this->setData(self::FIELD_MESSAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionIdentifier(): ?string
    {
        return $this->_getData(self::FIELD_TRANSACTION_IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionIdentifier(string $value): self
    {
        return $this->setData(self::FIELD_TRANSACTION_IDENTIFIER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBuyerIdentifier(): ?string
    {
        return $this->_getData(self::FIELD_BUYER_IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setBuyerIdentifier(string $value): self
    {
        return $this->setData(self::FIELD_BUYER_IDENTIFIER, $value);
    }
}
