<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Verification;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as RepositoryInterface;
use Line\VerifiedPurchase\Api\Verification\Gateway\Request\AttributeInterface;
use Line\VerifiedPurchase\Api\Verification\ResponseInterface;
use Line\VerifiedPurchase\Model\Adapter;
use Line\VerifiedPurchase\Model\Notification\Email;
use Line\VerifiedPurchase\Model\Order\StatusUpdaterAction;
use Line\VerifiedPurchase\Model\VerificationManager;
use Psr\Log\LoggerInterface;

/**
 * Solely in charge of the validation execution for a Verification
 */
class ValidateAction
{
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * @var Adapter
     */
    protected Adapter $adapter;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var VerificationManager
     */
    protected VerificationManager $manager;

    /**
     * @var StatusUpdaterAction
     */
    protected StatusUpdaterAction $statusUpdater;

    /**
     * @var Email
     */
    protected Email $notification;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Class constructor
     *
     * @param RepositoryInterface $repository
     * @param Adapter $adapter
     * @param ConfigInterface $config
     * @param VerificationManager $manager
     * @param StatusUpdaterAction $statusUpdater
     * @param Email $notification
     * @param LoggerInterface $logger
     */
    public function __construct(
        RepositoryInterface $repository,
        Adapter $adapter,
        ConfigInterface $config,
        VerificationManager $manager,
        StatusUpdaterAction $statusUpdater,
        Email $notification,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->adapter = $adapter;
        $this->config = $config;
        $this->manager = $manager;
        $this->statusUpdater = $statusUpdater;
        $this->notification = $notification;
        $this->logger = $logger;
    }

    /**
     * Executes a code validation for the given identifier
     *
     * @param string $identifier
     * @param string $code
     *
     * @return ResponseInterface|null
     */

    public function validateByIdentifier(string $identifier, string $code)
    {
        $verification = $this->repository->getByCustomerIdentifier($identifier);

        return $this->validate($verification, $code);
    }

    /**
     * Performs the code validation against the service
     *
     * The connector answers a rejected verification instead of throwing, so a null return here
     * means the attempt never reached the service: the caller has no gateway message to show.
     *
     * @param VerifyPurchaseInterface $verification
     * @param string $code
     *
     * @return ResponseInterface|null
     */
    public function validate(VerifyPurchaseInterface $verification, $code)
    {
        $attributes = [
            AttributeInterface::FIELD_CODE => $code,
            AttributeInterface::FIELD_VERIFIED_PURCHASE_ID => $verification->getVerifiedPurchaseId(),
            AttributeInterface::FIELD_MAX_TRIES => $this->config->getMaxTries()
        ];

        $response = null;

        try {
            /** @var ResponseInterface $response */
            $response = $this->adapter->validate($attributes);

            // Update and close verification, if validation succeed
            if ($response->getValidated()) {
                $verification->setTransactionIdentifier(
                    $response->getTransactionIdentifier()
                )->setBuyerIdentifier(
                    $response->getBuyerIdentifier()
                );

                $this->manager->markVerificationAsCompleted($verification);

                // Update Order status
                $this->statusUpdater->complete($verification);
                $this->notification->notifyComplete($verification);
            } else {
                // Increase intent into verification object
                $this->manager->recordVerificationIntent($verification);

                // If this try didn't succeeded `recordVerificationIntent()`
                // will mark as `failed` if necessary

                if ($verification->getIsFailed()) {
                    $this->statusUpdater->failed($verification);
                    $this->notification->notifyFailed($verification);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error(
                'Validate: Error during communication or data conversion',
                [$exception->getMessage()]
            );
        }

        return $response;
    }
}
