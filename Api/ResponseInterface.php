<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api;

use Line\VerifiedPurchase\Api\Response\AttributeInterface;

/**
 * Holds all request fields accepted by the Line service
 */
interface ResponseInterface extends AttributeInterface
{
    /**
     * Returns response status
     *
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * Sets response status
     *
     * @param bool $value
     *
     * @return self
     */
    public function setStatus(bool $value): self;

    /**
     * Returns process id to then be used for validation
     *
     * @return string
     */
    public function getVerifiedPurchaseId(): string;

    /**
     * Sets process id
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerifiedPurchaseId(string $value): self;

    /**
     * Returns verification channel value
     *
     * @return string
     */
    public function getVerificationChannel(): string;

    /**
     * Sets verification channel value
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerificationChannel(string $value): self;

    /**
     * Returns verification mode value
     *
     * @return string
     */
    public function getVerificationMode(): string;

    /**
     * Sets verification mode value
     *
     * @param string $value
     *
     * @return self
     */
    public function setVerificationMode(string $value): self;
}
