<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Controller\Verification;

use Line\VerifiedPurchase\Model\LastVerificationIdentifierAction;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;

class Form implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var LastVerificationIdentifierAction
     */
    protected LastVerificationIdentifierAction $session;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Class constructor
     *
     * @param PageFactory $resultPageFactory
     * @param RequestInterface $request
     * @param ForwardFactory $resultForwardFactory
     * @param LastVerificationIdentifierAction $session
     *
     */
    public function __construct(
        PageFactory $resultPageFactory,
        RequestInterface $request,
        ForwardFactory $resultForwardFactory,
        LastVerificationIdentifierAction $session
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $identifier = (string) $this->request->getParam('id');

        if (!$identifier) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $this->session->set($identifier);

        return $resultPage;
    }
}
