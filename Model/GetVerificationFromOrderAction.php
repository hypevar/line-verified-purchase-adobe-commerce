<?php

namespace Line\VerifiedPurchase\Model;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as RepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

class GetVerificationFromOrderAction
{
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * Class constructor
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Returns the Verification associated with the given Order
     *
     * @param OrderInterface $order
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function get(OrderInterface $order)
    {
        return $this->repository->getByOrderId($order->getEntityId());
    }

    /**
     * Returns the Verification associated with the Order Increment id
     *
     * @param string $increment
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $increment)
    {
        return $this->repository->getByIncrementId($increment);
    }
}
