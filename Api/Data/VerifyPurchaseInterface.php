<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Data;

interface VerifyPurchaseInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const ENTITY_ID = 'entity_id';
    public const FIELD_ORDER_ID = 'order_id';
    public const FIELD_INCREMENT_ID = 'increment_id';
    public const FIELD_IS_COMPLETED = 'is_completed';
    public const FIELD_IS_FAILED = 'is_failed';
    public const FIELD_FAILURES_NUM = 'failures_num';
    public const FIELD_CUSTOMER_ID = 'customer_id';
    public const FIELD_BUYER_EMAIL = 'buyer_email';
    public const FIELD_MASKED_CREDIT_CARD = 'masked_credit_card';
    public const FIELD_STATUS = 'status';
    public const FIELD_VERIFIED_PURCHASE_ID = 'verified_purchase_id';
    public const FIELD_CUSTOMER_IDENTIFIER = 'customer_identifier';
    public const FIELD_VERIFICATION_MODE = 'verification_mode';
    public const FIELD_VERIFICATION_CHANNEL = 'verification_channel';
    public const FIELD_TIME_UNIT = 'time_unit';
    public const FIELD_TIME_AMOUNT = 'time_amount';
    public const FIELD_TRANSACTION_IDENTIFIER = 'transaction_identifier';
    public const FIELD_BUYER_IDENTIFIER = 'buyer_identifier';
    /**#@-*/

    /**
     * Returns id value
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Returns associated order id
     *
     * @return int
     */
    public function getOrderId(): ?int;

    /**
     * Sets associated order id
     *
     * @param string|int $value
     *
     * @return self
     */
    public function setOrderId($value): self;

    /**
     * Returns Order increment id value
     *
     * @return string
     */
    public function getIncrementId(): string;

    /**
     * Sets Order increment id value
     *
     * @param string $value
     *
     * @return self
     */
    public function setIncrementId(string $value): self;

    /**
     * Returns
     *
     * @return bool
     */
    public function getIsCompleted(): bool;

    /**
     * Sets
     *
     * @param bool $value
     *
     * @return self
     */
    public function setIsCompleted(bool $value): self;

    /**
     * Returns
     *
     * @return int
     */
    public function getFailuresNum(): int;

    /**
     * Sets
     *
     * @param int $value
     *
     * @return self
     */
    public function setFailuresNum(int $value): self;

    /**
     * Returns
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * Sets
     *
     * @param int $value
     *
     * @return self
     */
    public function setCustomerId(int $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getBuyerEmail(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setBuyerEmail(string $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getMaskedCreditCard(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setMaskedCreditCard(string $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setStatus(string $value): self;

    /**
     * Returns
     *
     * @return string|null
     */
    public function getVerifiedPurchaseId(): ?string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerifiedPurchaseId(string $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getVerificationChannel(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerificationChannel(string $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getCustomerIdentifier(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setCustomerIdentifier(string $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getVerificationMode(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerificationMode(string $value): self;

    /**
     * Returns
     *
     * @return string
     */
    public function getTimeUnit(): string;

    /**
     * Sets
     *
     * @param string $value
     *
     * @return self
     */
    public function setTimeUnit(string $value): self;

    /**
     * Returns
     *
     * @return int
     */
    public function getTimeAmount(): int;

    /**
     * Sets
     *
     * @param int $value
     *
     * @return self
     */
    public function setTimeAmount(int $value): self;

    /**
     * Increases failure amount
     *
     * @return self
     */
    public function increaseTries(): self;

    /**
     * Returns identifier from success validate operation
     *
     * @return ?string
     */
    public function getTransactionIdentifier(): ?string;

    /**
     * Sets identifier from success validate operation
     *
     * @param string $value
     *
     * @return self
     */
    public function setTransactionIdentifier(string $value): self;

    /**
     * Returns buyer's identifier from success validate operation
     *
     * @return string
     */
    public function getBuyerIdentifier(): ?string;

    /**
     * Sets buyer's identifier from success validate operation
     *
     * @param string $value
     *
     * @return self
     */
    public function setBuyerIdentifier(string $value): self;

    /**
     * Whether validation process has failed
     *
     * Either by maximum tries, expiration or initially came
     * with errors from Gateway's response in `status` field
     *
     * @return bool
     */
    public function getIsFailed(): bool;

    /**
     * Set if the process needs to be considered as failed
     *
     * @param bool $value
     *
     * @return self
     */
    public function setIsFailed(bool $value): self;

    /**
     * Whether validation process is still pending
     *
     * @return bool
     */
    public function getIsPending(): bool;
}
