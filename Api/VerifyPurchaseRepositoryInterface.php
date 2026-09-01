<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api;

use Line\VerifiedPurchase\Api\Data\VerifiedPurchaseSearchResultsInterface;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Verification process repository interface
 */
interface VerifyPurchaseRepositoryInterface
{
    /**
     * Persists a verification object into the database
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return VerifyPurchaseInterface
     *
     * @throws CouldNotSaveException
     */
    public function save(VerifyPurchaseInterface $verification): VerifyPurchaseInterface;

    /**
     * Gets a verification by it's id
     *
     * @param string|int $id
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Gets a verification by the associated Order Id
     *
     * @param string|int $entityId
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function getByOrderId($entityId);

    /**
     * Gets a verification by the Customer Identifier request value
     *
     * @param string $identifier
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function getByCustomerIdentifier(string $identifier);

    /**
     * Gets a verification by the verification process value
     *
     * @param string $vid Verified Purchase Id
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function getByVerificationPurchaseId(string $vid);

    /**
     * Gets a verification by the Order Increment Id
     *
     * @param string $increment
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $increment);

    /**
     * Load data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return VerifiedPurchaseSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Deletes a verification from the database
     *
     * @param VerifyPurchaseInterface $verification
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(VerifyPurchaseInterface $verification);

    /**
     * Deletes a verification by using the object id
     *
     * @param string|int $id
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id);
}
