<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model\Notification;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Api\EmailNotificationInterface;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class Email implements EmailNotificationInterface
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var PaymentHelper
     */
    private PaymentHelper $paymentHelper;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param TransportBuilder $transportBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentHelper $paymentHelper
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        OrderRepositoryInterface $orderRepository,
        PaymentHelper $paymentHelper,
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->orderRepository = $orderRepository;
        $this->paymentHelper = $paymentHelper;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Send email notification to customer
     *
     * @param string $template
     * @param int $storeId
     * @param array $templateVars
     * @param string|array $from
     * @param string|array $recipient
     *
     * @return void
     */
    protected function sendEmail(
        string $template,
        int $storeId,
        array $templateVars,
        $from,
        $recipient
    ) {
        $options = [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];

        try {
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions($options)
                ->setTemplateVars($templateVars)
                ->setFromByScope($from, $storeId)
                ->addTo($recipient)
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function notifyPending(VerifyPurchaseInterface $verification)
    {
        $template = $this->config->getEmailProcessPendingTemplate();
        $configuredSender = $this->config->getEmailSender();
        $recipient = $verification->getBuyerEmail();

        $order = $this->orderRepository->get($verification->getOrderId());
        $storeId = (int) $order->getStoreId();

        $variables = [
            'order' => $order,
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'verification' => [
                'customer_identifier' => $verification->getCustomerIdentifier(),
                'verification_mode' => $verification->getVerificationMode(),
                'time_unit' => $verification->getTimeUnit(),
                'time_amount' => $verification->getTimeAmount()
            ],
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];

        $transportObject = new DataObject($variables);

        try {
            $this->sendEmail(
                $template,
                $storeId,
                $transportObject->getData(),
                $configuredSender,
                $recipient
            );
        } catch (\Exception $exception) {
            $this->logger->error('Unable to send Pending email message', [
                $verification->getEntityId(),
                $verification->getIncrementId(),
                $exception->getMessage()
            ]);

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function notifyComplete(VerifyPurchaseInterface $verification)
    {
        $template = $this->config->getEmailProcessCompleteTemplate();
        $configuredSender = $this->config->getEmailSender();
        $recipient = $verification->getBuyerEmail();

        $order = $this->orderRepository->get($verification->getOrderId());
        $storeId = (int) $order->getStoreId();

        $variables = [
            'order' => $order,
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];

        $transportObject = new DataObject($variables);

        try {
            $this->sendEmail(
                $template,
                $storeId,
                $transportObject->getData(),
                $configuredSender,
                $recipient
            );
        } catch (\Exception $exception) {
            $this->logger->error('Unable to send Complete email message', [
                $verification->getEntityId(),
                $verification->getIncrementId(),
                $exception->getMessage()
            ]);

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function notifyFailed(VerifyPurchaseInterface $verification)
    {
        $template = $this->config->getEmailProcessCanceledTemplate();
        $configuredSender = $this->config->getEmailSender();
        $recipient = $verification->getBuyerEmail();

        $order = $this->orderRepository->get($verification->getOrderId());
        $storeId = (int) $order->getStoreId();

        $variables = [
            'order' => $order,
            'store' => $order->getStore(),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];

        $transportObject = new DataObject($variables);

        try {
            $this->sendEmail(
                $template,
                $storeId,
                $transportObject->getData(),
                $configuredSender,
                $recipient
            );
        } catch (\Exception $exception) {
            $this->logger->error('Unable to send Failed email message', [
                $verification->getEntityId(),
                $verification->getIncrementId(),
                $exception->getMessage()
            ]);

            return false;
        }

        return true;
    }

    /**
     * Returns payment block as HTML
     *
     * @param OrderInterface $order
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getPaymentHtml(OrderInterface $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStoreId()
        );
    }
}
