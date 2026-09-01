<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Magento\Framework\Model\AbstractModel;

class VerifyPurchase extends AbstractModel implements VerifyPurchaseInterface
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = self::ENTITY_ID;

    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\VerifyPurchase::class);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): ?int
    {
        return (int) $this->_getData(self::FIELD_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value): self
    {
        $this->setData(self::FIELD_ORDER_ID, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIncrementId(): string
    {
        return $this->_getData(self::FIELD_INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIncrementId(string $value): self
    {
        $this->setData(self::FIELD_INCREMENT_ID, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsCompleted(): bool
    {
        return (bool) $this->_getData(self::FIELD_IS_COMPLETED);
    }

    /**
     * @inheritDoc
     */
    public function setIsCompleted(bool $value): self
    {
        $this->setData(self::FIELD_IS_COMPLETED, (int) $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFailuresNum(): int
    {
        return (int) $this->_getData(self::FIELD_FAILURES_NUM);
    }

    /**
     * @inheritDoc
     */
    public function setFailuresNum(int $value): self
    {
        $this->setData(self::FIELD_FAILURES_NUM, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): ?int
    {
        return (int) $this->_getData(self::FIELD_CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $value): self
    {
        $this->setData(self::FIELD_CUSTOMER_ID, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBuyerEmail(): string
    {
        return $this->_getData(self::FIELD_BUYER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setBuyerEmail(string $value): self
    {
        $this->setData(self::FIELD_BUYER_EMAIL, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaskedCreditCard(): string
    {
        return $this->_getData(self::FIELD_MASKED_CREDIT_CARD);
    }

    /**
     * @inheritDoc
     */
    public function setMaskedCreditCard(string $value): self
    {
        $this->setData(self::FIELD_MASKED_CREDIT_CARD, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->_getData(self::FIELD_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $value): self
    {
        $this->setData(self::FIELD_STATUS, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVerifiedPurchaseId(): ?string
    {
        return $this->_getData(self::FIELD_VERIFIED_PURCHASE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVerifiedPurchaseId(string $value): self
    {
        $this->setData(self::FIELD_VERIFIED_PURCHASE_ID, $value);
        return $this;
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
        $this->setData(self::FIELD_VERIFICATION_CHANNEL, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerIdentifier(): string
    {
        return $this->_getData(self::FIELD_CUSTOMER_IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerIdentifier(string $value): self
    {
        $this->setData(self::FIELD_CUSTOMER_IDENTIFIER, $value);
        return $this;
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
        $this->setData(self::FIELD_VERIFICATION_MODE, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTimeUnit(): string
    {
        return $this->_getData(self::FIELD_TIME_UNIT);
    }

    /**
     * @inheritDoc
     */
    public function setTimeUnit(string $value): self
    {
        $this->setData(self::FIELD_TIME_UNIT, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTimeAmount(): int
    {
        return (int) $this->_getData(self::FIELD_TIME_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setTimeAmount(int $value): self
    {
        $this->setData(self::FIELD_TIME_AMOUNT, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function increaseTries(): self
    {
        $this->setFailuresNum($this->getFailuresNum() + 1);
        return $this;
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
        $this->setData(self::FIELD_TRANSACTION_IDENTIFIER, $value);
        return $this;
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
        $this->setData(self::FIELD_BUYER_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsFailed(): bool
    {
        return (bool) $this->_getData(self::FIELD_IS_FAILED);
    }

    /**
     * @inheritDoc
     */
    public function setIsFailed(bool $value): self
    {
        $this->setData(self::FIELD_IS_FAILED, (int) $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsPending(): bool
    {
        return !$this->getIsCompleted() && !$this->getIsFailed();
    }
}
