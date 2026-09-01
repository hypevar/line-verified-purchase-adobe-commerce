<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Gateway\Request;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Request\AttributeInterface;
use Line\Payment\Api\Request\BuilderInterface;
use Line\Payment\Gateway\DataReader;
use Line\Payment\Model\GetTransactionIdentifierAction;
use Line\VerifiedPurchase\Model\CreateVerificationEntryAction;
use Line\VerifiedPurchase\Model\VerificationManager;
use Psr\Log\LoggerInterface;

class VerifiedBuilder implements BuilderInterface
{
    /**
     * @var DataReader
     */
    private DataReader $reader;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var GetTransactionIdentifierAction
     */
    private GetTransactionIdentifierAction $identifier;

    /**
     * @var CreateVerificationEntryAction
     */
    private CreateVerificationEntryAction $createAction;

    /**
     * @var VerificationManager
     */
    private VerificationManager $management;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ConfigInterface $config
     * @param DataReader $reader
     * @param GetTransactionIdentifierAction $action
     * @param CreateVerificationEntryAction $create
     * @param VerificationManager $management
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $config,
        DataReader $reader,
        GetTransactionIdentifierAction $action,
        CreateVerificationEntryAction $create,
        VerificationManager $management,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->reader = $reader;
        $this->identifier = $action;
        $this->createAction = $create;
        $this->management = $management;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        // exit if module isn't enabled
        if (!$this->config->isEnabled()) {
            return [];
        }

        $payment = $this->reader->readPayment($buildSubject);

        /** @var \Magento\Payment\Gateway\Data\OrderAdapterInterface $order */
        $order = $payment->getOrder();

        try {
            // @TODO: avoid generating another verification process for the same customer
            // previous check or flag needs to be made so we dont do this again
            if (!$this->management->isPaymentCandidateForVerification($payment)) {
                return [];
            }
        } catch (\Exception $exception) {
            //
            return [];
        }

        // Generate the custom identifier for this particular Order.
        //
        // It is keyed on the payment, not the order: `GetTransactionIdentifierAction` stores the
        // value in `additional_information` and returns the stored one on every later call, which
        // is what keeps this module and `Line_Payment` sending the same `IdentificadorCliente`
        // regardless of which builder runs first.
        $identifier = $this->identifier->generate($payment->getPayment());

        $request = [
            AttributeInterface::FIELD_CUSTOMER_IDENTIFIER => $identifier,
            AttributeInterface::FIELD_VERIFIED_PURCHASES => [
                AttributeInterface::FIELD_VERIFICATION_MODE => $this->config->getVerificationMode(),
                AttributeInterface::FIELD_TIME_UNIT => $this->config->getTimeUnit(),
                AttributeInterface::FIELD_TIME_AMOUNT => $this->config->getTimeAmount(),
            ]
        ];

        try {
            /**
             * Create the Verification Process entry
             * will get updated or deleted depending on how the Payment request ends
             *
             * @see \Line\VerifiedPurchase\Observer\BeforeDataConverter
             * @see \Line\VerifiedPurchase\Observer\UpdateVerificationAfterOrderPlace
             */
            $verification = $this->createAction->create($request, $order);

            $this->logger->debug(__(
                'Verification %1 Process created for Order #%2',
                $verification->getEntityId(),
                $order->getOrderIncrementId()
            ));
            $this->logger->debug('Verification Purchase request fields', [$request]);

        } catch (\Exception $exception) {
            $this->logger->error(__(
                'Verification could not be generated for Order #%1',
                $order->getOrderIncrementId()
            ));
            $this->logger->debug($exception->getMessage());
            $this->logger->debug('Verification Purchase request fields', [$request]);

            return [];
        }

        return $request;
    }
}
