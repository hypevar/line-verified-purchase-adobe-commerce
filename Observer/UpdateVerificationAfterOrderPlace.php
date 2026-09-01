<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Observer;

use Line\Payment\Api\Response\ErrorCodeInterface;
use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as RepositoryInterface;
use Line\VerifiedPurchase\Model\Notification\Email;
use Line\VerifiedPurchase\Model\Order\StatusUpdaterAction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

/**
 * Update Order details from Verification Purchase after order is placed
 *
 * @see \Line\VerifiedPurchase\Gateway\Request\VerifiedBuilder
 */
class UpdateVerificationAfterOrderPlace implements ObserverInterface
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
     * @var StatusUpdaterAction
     */
    private StatusUpdaterAction $statusUpdater;

    /**
     * @var Email
     */
    private Email $notification;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Class constructor
     *
     * @param ConfigInterface $config
     * @param RepositoryInterface $repository
     * @param StatusUpdaterAction $statusUpdater
     * @param Email $notification
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $config,
        RepositoryInterface $repository,
        StatusUpdaterAction $statusUpdater,
        Email $notification,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->statusUpdater = $statusUpdater;
        $this->notification = $notification;
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

        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $increment = $order->getIncrementId();

        // Order not saved yet...
        if (!$order->getEntityId()) {
            return;
        }

        try {
            // Ensure this Order needs to be evaluated
            /** @var VerifyPurchaseInterface $verification */
            $verification = $this->repository->getByIncrementId($increment);

            if (!$verification || !$verification->getEntityId()) {
                $this->logger->debug(__(
                    'Order %1 does not have an ongoing process, skipping...',
                    $increment
                ));
            }

            // Retrieve the Order Id
            $entityId = $order->getEntityId();

            // Payment Status
            $paymentStatus = (int) $order->getPayment()->getCcStatus();

            // Setup fields coming from gateway
            $verification->setOrderId($entityId)
                ->setBuyerEmail($order->getBillingAddress()->getEmail());

            // If payment didn't came through,
            // we shouldn't have to create a verification process
            if ($paymentStatus !== ErrorCodeInterface::CODE_AUTHORIZED) {
                // Update Order status and comment history
                $this->statusUpdater->failed($verification);
                $this->repository->save($verification);
                return;
            }

            // Set Order on proper status
            $this->statusUpdater->pending($verification);
            $this->repository->save($verification);

            // Notify there's a verification pending
            $this->notification->notifyPending($verification);

            //@TODO extract this process below into a the OrderService class
        } catch (NoSuchEntityException $exception) {
            // means this isn't a Verification Purchase process
            $this->logger->error(
                'Verification Update does not exists after Order placed:',
                [
                    $order->getEntityId(),
                    $exception->getMessage()
                ]
            );

        } catch (\Exception $exception) {
            $this->logger->error(
                'Verification Update error after Order placed:',
                [$exception->getMessage()]
            );
        }
    }
}
