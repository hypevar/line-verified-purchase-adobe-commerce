<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Plugin;

use Line\Payment\Api\Request\AttributeInterface as PaymentAttributeInterface;
use Line\Payment\Gateway\Request\DetailsDataBuilder;
use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Request\AttributeInterface;

class AddDetailsIntoRequest
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Class constructor
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Plugin after build method gets executed
     *
     * @param DetailsDataBuilder $subject
     * @param array $result
     *
     * @return array
     */
    public function afterBuild(
        DetailsDataBuilder $subject,
        array $result
    ): array {

        if (!$this->config->isEnabled()) {
            return $result;
        }

        // ensure we've details
        if (!is_array($result)
            || !isset($result[PaymentAttributeInterface::FIELD_DETAIL])
        ) {
            return $result;
        }

        $finalDetails = [];
        $details = $result[PaymentAttributeInterface::FIELD_DETAIL];

        // Set up all summary names
        // if split payment occurs, `$details` should be count() > 1
        foreach ($details as $value) {
            $value[
                AttributeInterface::FIELD_CREDIT_CARD_SUMMARY_DESCRIPTION
            ] = $this->config->getCreditCardSummaryDescription();

            array_push($finalDetails, $value);
        }

        return [
            PaymentAttributeInterface::FIELD_DETAIL => $finalDetails
        ];
    }
}
