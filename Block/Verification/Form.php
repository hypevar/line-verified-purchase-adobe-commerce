<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Block\Verification;

use Line\VerifiedPurchase\Api\Data\VerifyPurchaseInterface;
use Line\VerifiedPurchase\Model\Service\VerificationService;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Form extends Template
{
    /**
     * @var VerificationService
     */
    protected VerificationService $service;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Line_VerifiedPurchase::verification/validate/form.phtml';

    /**
     * Class constructor
     *
     * @param Context $context
     * @param VerificationService $service
     * @param array $data
     */
    public function __construct(
        Context $context,
        VerificationService $service,
        array $data = []
    ) {
        $this->service = $service;

        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    public function _toHtml()
    {
        if (!$this->getVerification()) {
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
        return $this->service->getVerification();
    }

    /**
     * Whether current verification is completed or not
     *
     * @return bool
     */
    public function getIsCompleted()
    {
        return $this->getVerification()->getIsCompleted();
    }

    /**
     * Whether current verification has failed or not
     *
     * @return bool
     */
    public function getIsFailed()
    {
        return $this->getVerification()->getIsFailed();
    }

    /**
     * Return the save action Url.
     *
     * @return string
     */
    public function getPostAction()
    {
        return $this->getUrl('verified_purchase/verification/validate');
    }

    /**
     * Returns Order Id
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->getVerification()->getIncrementId();
    }
}
