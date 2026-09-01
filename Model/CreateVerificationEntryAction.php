<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\Request\AttributeInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as RepositoryInterface;
use Line\VerifiedPurchase\Model\VerifyPurchaseFactory as VerifyFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;

/**
 * Creates a new verification process entry for a specific Order
 */
class CreateVerificationEntryAction
{
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * @var VerifyFactory
     */
    protected VerifyFactory $factory;

    /**
     * @param RepositoryInterface $repository
     * @param VerifyFactory $factory
     */
    public function __construct(
        RepositoryInterface $repository,
        VerifyFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * Create a Verification process entry for the given Order
     *
     * @param array $request
     * @param OrderAdapterInterface $order
     *
     * @throws CouldNotSaveException
     */
    public function create(
        $request,
        OrderAdapterInterface $order
    ): VerifyPurchaseInterface {

        try {
            /** @var VerifyPurchaseInterface $verification */
            $verification = $this->factory->create();

            $data = $request[AttributeInterface::FIELD_VERIFIED_PURCHASES];

            $identifier = $request[AttributeInterface::FIELD_CUSTOMER_IDENTIFIER];
            $unit = $data[AttributeInterface::FIELD_TIME_UNIT];
            $time = $data[AttributeInterface::FIELD_TIME_AMOUNT];
            $mode = $data[AttributeInterface::FIELD_VERIFICATION_MODE];

            /**
             * Setup all available fields
             * @see \Line\VerifiedPurchase\Observer\UpdateVerificationAfterOrderPlace
             */
            $verification->setIncrementId($order->getOrderIncrementId())
                ->setCustomerIdentifier($identifier)
                ->setTimeUnit($unit)
                ->setTimeAmount($time)
                ->setVerificationMode($mode);

            // Check, may be a Guest Order
            if ($cid = (int) $order->getCustomerId()) {
                $verification->setCustomerId($cid);
            }

            return $this->repository->save($verification);

        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }
}
