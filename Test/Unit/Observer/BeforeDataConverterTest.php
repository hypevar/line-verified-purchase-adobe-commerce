<?php
/**
 * Copyright © 2026 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Test\Unit\Observer;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\VerifyPurchaseRepositoryInterface;
use Line\VerifiedPurchase\Model\Order\StatusUpdaterAction;
use Line\VerifiedPurchase\Model\Service\CreditCardNumberMaskService;
use Line\VerifiedPurchase\Observer\BeforeDataConverter;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * The observer runs on `line_payment_data_converter_before`, which is dispatched after the gateway
 * has already answered. By then the card has been charged, so nothing this module does may
 * propagate out of `execute()`: an exception here destroys the order and leaves the charge
 * stranded with nothing to reconcile it against.
 */
class BeforeDataConverterTest extends TestCase
{
    /** @var ConfigInterface&MockObject */
    private $config;

    /** @var VerifyPurchaseRepositoryInterface&MockObject */
    private $repository;

    /** @var LoggerInterface&MockObject */
    private $logger;

    private BeforeDataConverter $observer;

    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->repository = $this->createMock(VerifyPurchaseRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->config->method('isEnabled')->willReturn(true);

        $this->observer = new BeforeDataConverter(
            $this->config,
            $this->repository,
            $this->createMock(CreditCardNumberMaskService::class),
            $this->createMock(StatusUpdaterAction::class),
            $this->logger
        );
    }

    /**
     * The regression that lost order 000000035: the module's table was missing, the lookup threw
     * SQLSTATE[42S02], and it escaped a catch that only named NoSuchEntityException.
     */
    public function testSwallowsAnInfrastructureFailureFromTheLookup(): void
    {
        $this->repository->method('getByCustomerIdentifier')->willThrowException(
            new \RuntimeException(
                "SQLSTATE[42S02]: Base table or view not found: 1146 "
                . "Table 'magento.verified_purchase_customer_order' doesn't exist"
            )
        );

        $this->logger->expects($this->once())->method('critical');

        $this->observer->execute($this->observerWith(['IdentificadorCliente' => 'abc123']));
    }

    /**
     * A fatal error is just as destructive as an exception on this code path, and a TypeError is
     * one bad `additional_information` read away.
     */
    public function testSwallowsAnErrorFromTheLookup(): void
    {
        $this->repository->method('getByCustomerIdentifier')->willThrowException(
            new \TypeError('getByCustomerIdentifier(): Argument #1 must be of type string, null given')
        );

        $this->logger->expects($this->once())->method('critical');

        $this->observer->execute($this->observerWith(['IdentificadorCliente' => 'abc123']));
    }

    /**
     * The ordinary case — this payment has no verification — must stay silent.
     */
    public function testAPaymentWithoutAVerificationIsNotLogged(): void
    {
        $this->repository->method('getByCustomerIdentifier')->willThrowException(
            new \Magento\Framework\Exception\NoSuchEntityException(__('not found'))
        );

        $this->logger->expects($this->never())->method('critical');
        $this->logger->expects($this->never())->method('error');

        $this->observer->execute($this->observerWith(['IdentificadorCliente' => 'abc123']));
    }

    /**
     * @param array $rawResponse
     *
     * @return Observer
     */
    private function observerWith(array $rawResponse): Observer
    {
        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->addMethods(['getRawResponse'])
            ->getMock();

        $event->method('getRawResponse')->willReturn($rawResponse);

        $observer = $this->createMock(Observer::class);
        $observer->method('getEvent')->willReturn($event);

        return $observer;
    }
}
