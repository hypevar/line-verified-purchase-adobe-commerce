<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Block\Sales\Order;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Model\GetVerificationFromOrderAction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class Notification extends Template
{
    /**
     * @var GetVerificationFromOrderAction
     */
    protected GetVerificationFromOrderAction $action;

    /**
     * @var VerifyPurchaseInterface
     */
    protected VerifyPurchaseInterface $verification;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $builder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * Block template file
     *
     * @var string
     */
    protected $_template = 'Line_VerifiedPurchase::sales/order/notification.phtml';

    /**
     * Class constructor
     *
     * @param Registry $registry
     * @param GetVerificationFromOrderAction $action
     * @param UrlInterface $urlBuilder
     * @param Context $context
     * @param array $data = []
     */
    public function __construct(
        Registry $registry,
        GetVerificationFromOrderAction $action,
        UrlInterface $urlBuilder,
        Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->action = $action;
        $this->builder = $urlBuilder;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * @inheritDoc
     */
    public function _toHtml()
    {
        try {
            // Check if this Order has a verification process
            $verification = $this->action->getByIncrementId(
                $this->getOrder()->getIncrementId()
            );
            $this->verification = $verification;

        } catch (NoSuchEntityException $exception) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Returns current verification
     *
     * @return VerifyPurchaseInterface
     */
    public function getVerification()
    {
        return $this->verification;
    }

    /**
     * Returns url to the validation form for the current verification process
     *
     * @return string
     */
    public function getNotificationUrl()
    {
        return $this->builder->getUrl(
            'verified_purchase/verification/form',
            ['id' => $this->verification->getCustomerIdentifier()]
        );
    }
}
