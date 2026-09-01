<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Verification;

use Line\VerifiedPurchase\Api\Verification\Response\AttributeInterface;

/**
 * Holds all response fields that comes during a single Verification call
 *
 * This response is from the verification code operation.
 *
 * Meaning: When a Customer that's already in the middle
 *  of the verification process tries to verify a code
 */
interface ResponseInterface extends AttributeInterface
{
    /**
     * Returns if verification was validated successfully or not
     *
     * @return bool
     */
    public function getValidated(): bool;

    /**
     * Sets if verification was validated
     *
     * @param bool $value
     *
     * @return self
     */
    public function setValidated(bool $value): self;

    /**
     * Returns verification purchase id value
     *
     * @return string
     */
    public function getVerifiedPurchaseId(): string;

    /**
     * Sets the verification purchase id value
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerifiedPurchaseId(string $value): self;

    /**
     * Returns service message which is filled out in a fail case
     *
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * Sets service message value
     *
     * @param string $value
     *
     * @return self
     */
    public function setMessage(string $value): self;

    /**
     * Returns the transaction identifier for the current process
     *
     * @return string
     */
    public function getTransactionIdentifier(): ?string;

    /**
     * Sets the transaction identifier
     *
     * @param string $value
     *
     * @return self
     */
    public function setTransactionIdentifier(string $value): self;

    /**
     * Returns the buyer identifier
     *
     * @return string
     */
    public function getBuyerIdentifier(): ?string;

    /**
     * Sets the buyer identifier
     *
     * @param string $value
     *
     * @return self
     */
    public function setBuyerIdentifier(string $value): self;
}
