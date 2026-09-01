<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

namespace Line\VerifiedPurchase\Model;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface;
use Line\VerifiedPurchase\Model\ResourceModel\VerifyPurchase as ResourceModel;
use Line\VerifiedPurchase\Model\VerifyPurchaseFactory;
use Line\VerifiedPurchase\Api\Data\VerifiedPurchaseSearchResultsInterfaceFactory as ResultsInterface;
use Line\VerifiedPurchase\Model\ResourceModel\VerifyPurchase\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class VerifyPurchaseRepository implements VerifyPurchaseRepositoryInterface
{
    /**
     * @var VerifyPurchaseFactory
     */
    protected VerifyPurchaseFactory $factory;

    /**
     * @var ResourceModel
     */
    protected ResourceModel $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected CollectionProcessorInterface $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var ResultsInterface
     */
    protected ResultsInterface $searchResultsFactory;

    /**
     * Class constructor
     *
     * @param VerifyPurchaseFactory $factory
     * @param ResourceModel $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     * @param ResultsInterface $searchResultsFactory
     */
    public function __construct(
        VerifyPurchaseFactory $factory,
        ResourceModel $resource,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory,
        ResultsInterface $searchResultsFactory
    ) {
        $this->factory = $factory;
        $this->resource = $resource;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(VerifyPurchaseInterface $verification): VerifyPurchaseInterface
    {
        try {
            $this->resource->save($verification);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $verification;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        /** @var VerifyPurchase $verification */
        $verification = $this->factory->create();

        $this->resource->load($verification, $id);

        if (!$verification->getId()) {
            throw new NoSuchEntityException(__('The verification with the "%1" ID doesn\'t exist.', $id));
        }

        return $verification;
    }

    /**
     * Loads an entity by specifc field
     *
     * @param string|int $value
     * @param string $field
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    protected function loadByField($value, $field = 'entity_id')
    {
        /** @var VerifyPurchase $verification */
        $verification = $this->factory->create();

        $this->resource->load($verification, $value, $field);

        if (!$verification->getId()) {
            throw new NoSuchEntityException(__(
                'The verification with the field "%1" and value "%2" doesn\'t exist.',
                $field,
                $value
            ));
        }

        return $verification;
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId($entityId)
    {
        return $this->loadByField(
            $entityId,
            VerifyPurchase::FIELD_ORDER_ID
        );
    }

    /**
     * @inheritDoc
     */
    public function getByCustomerIdentifier(string $identifier)
    {
        return $this->loadByField(
            $identifier,
            VerifyPurchase::FIELD_CUSTOMER_IDENTIFIER
        );
    }

    /**
     * @inheritDoc
     */
    public function getByVerificationPurchaseId(string $vid)
    {
        return $this->loadByField(
            $vid,
            VerifyPurchase::FIELD_VERIFIED_PURCHASE_ID
        );
    }

    /**
     * @inheritDoc
     */
    public function getByIncrementId(string $increment)
    {
        return $this->loadByField(
            $increment,
            VerifyPurchase::FIELD_INCREMENT_ID
        );
    }

    /**
     * Load data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return VerifiedPurchaseSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var CollectionFactory $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var ResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete($verification)
    {
        try {
            /** @var VerifyPurchase $verification */
            $this->resource->delete($verification);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
