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

class Result extends Template
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
    protected $_template = 'Line_VerifiedPurchase::verification/validate/result.phtml';

    /**
     * Class constructor
     *
     * @param Context $context
     * @param VerificationService $service
     * @param array $data = []
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
     * Returns current verification
     *
     * @return VerifyPurchaseInterface|null
     */
    public function getVerification()
    {
        return $this->service->getVerification();
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
     * Returns form url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'verified_purchase/verification/form',
            ['id' => $this->service->getVerificationIdentifier()]
        );
    }
}
