<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Service;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface as Repository;
use Line\VerifiedPurchase\Model\LastVerificationIdentifierAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class VerificationService
{
    /**
     * @var VerifyPurchaseInterface|null
     */
    private ?VerifyPurchaseInterface $current = null;

    /**
     * @var LastVerificationIdentifierAction
     */
    private LastVerificationIdentifierAction $last;

    /**
     * @var Repository
     */
    private Repository $repository;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Class constructor
     *
     * @param LastVerificationIdentifierAction $last
     * @param Repository $repository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        LastVerificationIdentifierAction $last,
        Repository $repository,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->last = $last;
        $this->repository = $repository;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Retrieves the verification customer identifier
     *
     * @return null|string
     */
    public function getVerificationIdentifier()
    {
        // Try getting it from session
        $session = $this->last->get();
        // and from url
        $url = $this->request->getParam('id');

        if ($session === null && $url === null) {
            return null;
        }

        if ($url && $url != $session) {
            $this->last->set($url);
        }

        return $this->last->get();
    }

    /**
     * Tries to return a verification object by using the current session data
     *
     * @return VerifyPurchaseInterface|null
     * @throws NoSuchEntityException
     */
    public function getVerification()
    {
        $identifier = $this->getVerificationIdentifier();

        // same verification
        if ($this->current
            && $this->current->getCustomerIdentifier() === $identifier
        ) {
            return $this->current;
        }

        if (!$identifier) {
            return null;
        }

        try {
            $this->current = $this->repository->getByCustomerIdentifier($identifier);

        } catch (NoSuchEntityException $exception) {
            $this->logger->error(
                'VerificationService: Could not retrieve verification',
                [$exception->getMessage()]
            );

            return null;
        }

        return $this->current;
    }
}
