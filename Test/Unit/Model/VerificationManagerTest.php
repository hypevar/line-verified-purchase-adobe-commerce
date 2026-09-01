<?php
/**
 * Copyright © 2026 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Test\Unit\Model;

use Line\Payment\Api\Data\Checkout\SensitiveDataInterface;
use Line\Payment\Model\Checkout\SensitiveDataRegistry;
use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifiedPurchaseSearchResultsInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface;
use Line\VerifiedPurchase\Model\Service\CreditCardNumberMaskService;
use Line\VerifiedPurchase\Model\VerificationManager;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Payment 0.5.0 stopped copying the PAN into `additional_information` — `DataAssignObserver`
 * dropped CREDIT_CARD_NUMBER from `$additionalFields` and routes it to a request scoped registry
 * instead. This module has to read it from the same place.
 */
class VerificationManagerTest extends TestCase
{
    /** @var VerifyPurchaseRepositoryInterface&MockObject */
    private $repository;

    /** @var CreditCardNumberMaskService&MockObject */
    private $ccmask;

    /** @var SensitiveDataRegistry&MockObject */
    private $registry;

    /** @var LoggerInterface&MockObject */
    private $logger;

    private VerificationManager $manager;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(VerifyPurchaseRepositoryInterface::class);
        $this->ccmask = $this->createMock(CreditCardNumberMaskService::class);
        $this->registry = $this->createMock(SensitiveDataRegistry::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $criteria = $this->createMock(SearchCriteriaBuilder::class);
        $criteria->method('addFilter')->willReturnSelf();
        $criteria->method('create')->willReturn(new \Magento\Framework\Api\SearchCriteria());

        $filters = $this->createMock(FilterBuilder::class);
        $filters->method('setField')->willReturnSelf();
        $filters->method('setValue')->willReturnSelf();
        $filters->method('create')->willReturn(new \Magento\Framework\Api\Filter());

        $this->manager = new VerificationManager(
            $this->repository,
            $this->createMock(ConfigInterface::class),
            $this->ccmask,
            $criteria,
            $filters,
            $this->logger,
            $this->registry
        );
    }

    /**
     * The PAN must come from the registry, not from `additional_information`.
     */
    public function testTheCardNumberIsReadFromTheSensitiveDataRegistry(): void
    {
        $card = $this->createMock(SensitiveDataInterface::class);
        $card->method('getPan')->willReturn('4050719999999999');

        $this->registry->method('get')->willReturn($card);

        $this->ccmask->expects($this->once())
            ->method('getCardNumberPlaceholder')
            ->with('4050719999999999')
            ->willReturn('405071******9999');

        $results = $this->createMock(VerifiedPurchaseSearchResultsInterface::class);
        $results->method('getTotalCount')->willReturn(0);
        $this->repository->method('getList')->willReturn($results);

        $this->assertTrue(
            $this->manager->isPaymentCandidateForVerification($this->paymentWithEmail('buyer@example.com'))
        );
    }

    /**
     * The regression: with no card in the registry the old code read a missing array key and then
     * handed null to a `string` parameter. That TypeError is an Error, so the method's own
     * `catch (\Exception)` never held it and it escaped into the request builder.
     */
    public function testAnEmptyRegistryDoesNotRaise(): void
    {
        $this->registry->method('get')->willReturn(null);

        $this->ccmask->expects($this->never())->method('getCardNumberPlaceholder');

        $this->assertTrue(
            $this->manager->isPaymentCandidateForVerification($this->paymentWithEmail('buyer@example.com'))
        );
    }

    /**
     * @param string $email
     *
     * @return PaymentDataObjectInterface&MockObject
     */
    private function paymentWithEmail(string $email)
    {
        $address = new \Magento\Framework\DataObject(['email' => $email]);

        $order = $this->createMock(\Magento\Payment\Gateway\Data\OrderAdapterInterface::class);
        $order->method('getBillingAddress')->willReturn($address);

        $payment = $this->createMock(PaymentDataObjectInterface::class);
        $payment->method('getOrder')->willReturn($order);

        return $payment;
    }
}
