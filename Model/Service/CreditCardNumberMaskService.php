<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Service;

use Magento\Framework\Encryption\EncryptorInterface;

class CreditCardNumberMaskService
{
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * Class constructor
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * Obfuscate credit card value to be saved into database
     *
     * @param string $value
     *
     * @return string
     */
    public function encrypt(string $value): string
    {
        return $this->encryptor->encrypt($value);
    }

    /**
     * Obfuscate credit card value to be saved into database
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt(string $value): string
    {
        return $this->encryptor->decrypt($value);
    }

    /**
     * Creates a placeholder for the Credit Card number
     *
     * @param string $number
     *
     * @return string
     */
    public function getCardNumberPlaceholder(string $number): string
    {
        $first = substr($number, 0, 6);
        $last = substr($number, strlen($number) - 4);
        $total = strlen($number);
        $fill = $total - (strlen($first) + strlen($last));

        return $first . str_repeat('*', $fill) . $last;
    }
}
