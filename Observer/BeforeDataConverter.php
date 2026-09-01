<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Observer;

use Line\Payment\Api\Response\GatewayAttributeInterface;
use Line\Payment\Api\Response\StatusInterface;
use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\Response\Attribute\StatusInterface as AttributeStatusInterface;
use Line\VerifiedPurchase\Api\Response\AttributeInterface as GatewayResponseAttributeInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as RepositoryInterface;
use Line\VerifiedPurchase\Model\Order\StatusUpdaterAction;
use Line\VerifiedPurchase\Model\Service\CreditCardNumberMaskService;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Event that handles response information from Payment Gateway
 */
class BeforeDataConverter implements ObserverInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $repository;

    /**
     * @var CreditCardNumberMaskService
     */
    private CreditCardNumberMaskService $ccmask;

    /**
     * @var StatusUpdaterAction
     */
    private StatusUpdaterAction $statusUpdater;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Class constructor
     *
     * @param ConfigInterface $config
     * @param RepositoryInterface $repository
     * @param CreditCardNumberMaskService $ccmask
     * @param StatusUpdaterAction $statusUpdater
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $config,
        RepositoryInterface $repository,
        CreditCardNumberMaskService $ccmask,
        StatusUpdaterAction $statusUpdater,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->ccmask = $ccmask;
        $this->statusUpdater = $statusUpdater;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $data = $observer->getEvent()->getRawResponse();

        $identifier = $data[GatewayAttributeInterface::FIELD_CUSTOMER_IDENTIFIER] ?? '';

        // We're catching this first, to avoid generating an error log entry
        try {
            // Ensure this Order needs to be evaluated
            /** @var VerifyPurchaseInterface $verification */
            $verification = $this->repository->getByCustomerIdentifier($identifier);

            // Order does not have an ongoing process, skipping...
            if (!$verification->getEntityId()) {
                return;
            }
        } catch (NoSuchEntityException $exception) {
            // This payment has no verification process. The ordinary case, not worth logging.
            return;
        } catch (\Throwable $exception) {
            // By the time this observer runs the gateway has already answered, so the card may
            // already be charged. Letting anything escape from here destroys the order and leaves
            // that charge with nothing to reconcile it against, which is exactly what a missing
            // `verified_purchase_customer_order` table did. No failure of this module is worth
            // that, so it is logged and swallowed.
            $this->logger->critical(
                'Verified Purchase: could not read the verification, skipping.',
                ['error' => $exception->getMessage(), 'identifier' => $identifier]
            );

            return;
        }

        try {
            // Retrieve Payment status from Gateway response
            $paymentStatus = $data[GatewayAttributeInterface::FIELD_STATUS]
                ?? '-unknown-';

            // Remove verification process entry if Payment did not succeeded
            if ($paymentStatus !== StatusInterface::STATUS_AUTHORIZED) {
                $this->logger->error(
                    __(
                        'Verification Process %1 removal due Payment Error %2 for Order #%3',
                        [
                            $verification->getEntityId(),
                            $paymentStatus,
                            $verification->getIncrementId()
                        ]
                    )
                );

                $this->repository->delete($verification);
                return;
            }

            // Get specific Verified Purchase response from Gateway
            $response = $data[GatewayResponseAttributeInterface::FIELD_VERIFIED_PURCHASE] ?? false;

            // Gateway Response does not contain process relevant information
            if (!$response) {
                $this->logger->debug(__(
                    'Payment Gateway Response '
                    . 'does not have a verified-purchase field, skipping...'
                ), [json_encode($data)]);
                return;
            }

            // Verified Purchase ID
            $vid = $response[GatewayResponseAttributeInterface::FIELD_VERIFIED_PURCHASE_ID] ?? '';

            // Verification Channel
            $channel = $response[GatewayResponseAttributeInterface::FIELD_VERIFICATION_CHANNEL] ?? '';

            // Gateway status regarding process initialization (OK, ERROR)
            $status = $response[GatewayResponseAttributeInterface::FIELD_STATUS] ?? '';

            // Credit Card masked for later validation (avoid starting a process twice)
            $placeHolderCreditCard = $data[GatewayAttributeInterface::FIELD_CREDIT_CARD_NUMBER] ?? '';

            // Assing values into the verification object
            $verification->setVerifiedPurchaseId($vid)
                ->setVerificationChannel($channel)
                ->setStatus($status)
                ->setMaskedCreditCard(
                    $this->ccmask->encrypt($placeHolderCreditCard)
                );

            // Mark as failed if process initialization comes with an error,
            // from the external service response
            if ($status === AttributeStatusInterface::STATUS_ERROR) {
                $this->statusUpdater->failed($verification);
            }

            $this->repository->save($verification);

        } catch (\Throwable $exception) {
            // Same reasoning as the lookup above: an Error here is as destructive as an Exception.
            $this->logger->error(
                'Exception during BeforeDataConverter:',
                [$exception->getMessage(), $data]
            );
        }
    }
}
