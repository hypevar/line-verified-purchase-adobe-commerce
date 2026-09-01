<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Line\Payment\Model\Checkout\SensitiveDataRegistry;
use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifiedPurchaseSearchResultsInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as RepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Model\Service\CreditCardNumberMaskService;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Psr\Log\LoggerInterface;

class VerificationManager
{
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var CreditCardNumberMaskService
     */
    protected CreditCardNumberMaskService $ccmask;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $criteria;

    /**
     * @var FilterBuilder
     */
    protected FilterBuilder $filterBuilder;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var SensitiveDataRegistry
     */
    protected SensitiveDataRegistry $registry;

    /**
     * Class constructor
     *
     * @param RepositoryInterface $repository
     * @param ConfigInterface $config
     * @param CreditCardNumberMaskService $ccmask
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filters
     * @param LoggerInterface $logger
     * @param SensitiveDataRegistry $registry
     */
    public function __construct(
        RepositoryInterface $repository,
        ConfigInterface $config,
        CreditCardNumberMaskService $ccmask,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filters,
        LoggerInterface $logger,
        SensitiveDataRegistry $registry
    ) {
        $this->repository = $repository;
        $this->config = $config;
        $this->ccmask = $ccmask;
        $this->criteria = $criteriaBuilder;
        $this->filterBuilder = $filters;
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * Returns a verificatino instance
     *
     * @param VerifyPurchaseInterface|string $verification
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    private function getVerification($verification)
    {
        if (is_string($verification)) {
            $verification = $this->repository->getByCustomerIdentifier($verification);
        }

        return $verification;
    }

    /**
     * Sets all required fields to mark the Verification as completed
     *
     * @param VerifyPurchaseInterface|string $verification
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function markVerificationAsCompleted($verification)
    {
        $verification = $this->getVerification($verification);
        $verification->setIsCompleted(true)
            ->setIsFailed(false);

        return $this->repository->save($verification);
    }

    /**
     * Check if failures are equal or greater than the current configured max tries
     *
     * @param VerifyPurchaseInterface|string $verification
     *
     * @return bool
     */
    public function hasReachedMaxTries($verification)
    {
        $verification = $this->getVerification($verification);
        $maxTries = (int) $this->config->getMaxTries();

        return (bool) ($verification->getFailuresNum() >= $maxTries);
    }

    /**
     * Records a new intent of verifying
     *
     * @param VerifyPurchaseInterface|string $verification
     *
     * @return VerifyPurchaseInterface
     *
     * @throws NoSuchEntityException
     */
    public function recordVerificationIntent($verification)
    {
        /**
         * @var VerifyPurchaseInterface $verification
         */
        $verification = $this->getVerification($verification);

        // Increase intent
        $verification->increaseTries();

        // Check if that was the last one and mark as completed
        if ($this->hasReachedMaxTries($verification)) {
            $verification->setIsFailed(true);
        }

        return $this->repository->save($verification);
    }

    /**
     * Check if we've to create a verification process for the given Payment
     *
     * During request execution, we'll get the entered CC number
     * generate the placeholder that the gateway returns (when request comes back in)
     *
     * @param PaymentDataObjectInterface $payment
     */
    public function isPaymentCandidateForVerification(
        PaymentDataObjectInterface $payment
    ) {
        // Payment 0.5.0 stopped copying the PAN into `additional_information` and routes it to a
        // request scoped registry instead, so that is the only place it can be read from now.
        $card = $this->registry->get();

        if ($card === null) {
            $this->logger->error(
                'Verified Purchase: no card data in the request scope, '
                . 'treating the payment as a verification candidate.'
            );

            return true;
        }

        /** @var OrderAdapterInterface $order */
        $order = $payment->getOrder();

        try {
            $placeholder = $this->ccmask->getCardNumberPlaceholder($card->getPan());
            $email = $order->getBillingAddress()->getEmail();

            return $this->isCandidateForVerification($placeholder, $email);

        } catch (\Exception $exception) {
            $this->logger->error(
                'Cannot access to Order nor Payment information',
                [$exception->getMessage()]
            );
        }

        return true;
    }

    /**
     * Whether credit card mask and email already have a verification process completed or not
     *
     * @param string $creditCard Credit Card number masked
     * @param string $email Buyer email
     *
     * @return bool
     */
    public function isCandidateForVerification(
        string $creditCard,
        string $email
    ): bool {
        // @TODO: introduce feature for Store ID filtering
        $searchCriteria = $this->criteria->addFilter(
            $this->filterBuilder->setField(VerifyPurchaseInterface::FIELD_BUYER_EMAIL)
                ->setValue($email)
                ->create()
        )->addFilter(
            $this->filterBuilder->setField(VerifyPurchaseInterface::FIELD_IS_COMPLETED)
                ->setValue(1)
                ->create()
        )->create();

        /** @var VerifiedPurchaseSearchResultsInterface $list */
        $list = $this->repository->getList($searchCriteria);

        // No process with `complete` flag for that email is found
        if ($list->getTotalCount() === 0) {
            return true;
        }

        $result = true;

        // Find if this credit card placeholder exists
        // between the results
        foreach ($list->getItems() as $process) {
            /** @var VerifyPurchaseInterface $process */
            $plain = $this->ccmask->decrypt($process->getMaskedCreditCard());

            // if we find at least one, there's need to create a new process
            if ($plain === $creditCard) {
                return false;
            }
        }

        return $result;
    }
}
