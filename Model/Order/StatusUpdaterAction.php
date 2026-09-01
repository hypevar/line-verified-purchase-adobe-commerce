<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Order;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Psr\Log\LoggerInterface;

/**
 * Updates the Order based on what is configured for each scenario
 */
class StatusUpdaterAction
{
    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var OrderManagementInterface
     */
    protected OrderManagementInterface $orderManagement;

    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var HistoryFactory
     */
    protected HistoryFactory $history;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ConfigInterface $config
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param HistoryFactory $history
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigInterface $config,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        HistoryFactory $history,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->orderManagement = $orderManagement;
        $this->orderRepository = $orderRepository;
        $this->history = $history;
        $this->logger = $logger;
    }

    /**
     * Retrieves an order by it's verification object associated
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return OrderInterface
     */
    protected function getOrderFromVerification(
        VerifyPurchaseInterface $verification
    ): OrderInterface {
        return $this->orderRepository->get($verification->getOrderId());
    }

    /**
     * Sets the verification and order in a Pending status
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     */
    public function pending(VerifyPurchaseInterface $verification)
    {
        $status = $this->config->getOrderStatusPending();
        $comment = 'Verification Purchase process is pending';
        $order = $this->getOrderFromVerification($verification);

        $verification->setIsCompleted(false)
            ->setIsFailed(false);

        return $this->persistStatus($order, $status, $comment);
    }

    /**
     * Sets the verification and order in a Complete status
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     */
    public function complete(VerifyPurchaseInterface $verification)
    {
        $status = $this->config->getOrderStatusCompleted();
        $comment = 'Verification Purchase process finished successfully';
        $order = $this->getOrderFromVerification($verification);

        $verification->setIsCompleted(true)
            ->setIsFailed(false);

        return $this->persistStatus($order, $status, $comment);
    }

    /**
     * Sets the verification and order in a Failed status
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     */
    public function failed(VerifyPurchaseInterface $verification)
    {
        $status = $this->config->getOrderStatusFailed();
        $comment = 'Verification Purchase process failed';
        $order = $this->getOrderFromVerification($verification);

        $verification->setIsCompleted(false)
            ->setIsFailed(true);

        return $this->persistStatus($order, $status, $comment);
    }

    /**
     * In charge to update the status of an Order
     *
     * @param OrderInterface $order Order to which the status needs to be updated
     * @param string $status Order Status
     * @param string $comment Order Status comment
     * @param bool $isVisibleOnFront whether to show the comment into the Storefront
     * @param string $entityName to which entity the comment belongs
     *
     * @return bool
     */
    public function persistStatus(
        OrderInterface $order,
        string $status,
        string $comment = '',
        bool $isVisibleOnFront = true,
        string $entityName = 'payment'
    ) {
        // retrieve Order id
        $id = $order->getEntityId();

        $statusHistory = $this->history->create()
            ->setStatus($status)
            ->setEntityName($entityName)
            ->setIsVisibleOnFront($isVisibleOnFront);

        // set comment into the history status change
        if ($comment) {
            $statusHistory->setComment(__($comment));
        }

        return $this->orderManagement->addComment($id, $statusHistory);
    }
}
