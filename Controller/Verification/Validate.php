<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Controller\Verification;

use Line\VerifiedPurchase\Api\Verification\ResponseInterface;
use Line\VerifiedPurchase\Model\Service\VerificationService;
use Line\VerifiedPurchase\Model\Verification\ValidateAction;
use Line\VerifiedPurchase\Model\VerificationManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Validate implements HttpPostActionInterface
{
    /**
     * @var ResultRedirectFactory
     */
    private ResultRedirectFactory $resultRedirectFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var MessageManagerInterface
     */
    private MessageManagerInterface $messageManager;

    /**
     * @var ValidateAction
     */
    private ValidateAction $action;

    /**
     * @var VerificationService
     */
    private VerificationService $service;

    /**
     * @var VerificationManager
     */
    private VerificationManager $manager;

    /**
     * @var FormKeyValidator
     */
    private FormKeyValidator $formKeyValidator;

    /**
     * Class constructor
     *
     * @param Context $context
     * @param ValidateAction $action
     * @param VerificationService $service
     * @param VerificationManager $manager
     * @param FormKeyValidator $formKeyValidator
     */
    public function __construct(
        Context $context,
        ValidateAction $action,
        VerificationService $service,
        VerificationManager $manager,
        FormKeyValidator $formKeyValidator
    ) {
        $this->request = $context->getRequest();
        $this->messageManager = $context->getMessageManager();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->action = $action;
        $this->service = $service;
        $this->manager = $manager;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $validationCode = $this->request->getParam('code');
        $idParam = $this->request->getParam('id');

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->request->isPost()
            || !$validationCode
            || empty($validationCode)
        ) {
            $this->messageManager->addErrorMessage(
                __('Introduce the validation code or contact us for assistance.')
            );

            return $resultRedirect->setPath('*/*/form', ['id' => $idParam]);
        }

        try {
            $this->validateFormKey();

            // get current verification
            $verification = $this->service->getVerification();

            if (!$verification
                || !$verification->getVerifiedPurchaseId()
                || $verification->getIsCompleted()
                || $verification->getIsFailed()
            ) {
                throw new LocalizedException(__('No purchase id found for this verification'));
            }

            /** @var ResponseInterface|null $response */
            $response = $this->action->validate($verification, $validationCode);

            if (!$response || !$response->getValidated()) {
                $this->messageManager->addErrorMessage(
                    $response
                        ? $response->getMessage()
                        : __('The verification could not be completed. Please try again.')
                );

                return $resultRedirect->setPath('*/*/result', [
                    'id' => $verification->getCustomerIdentifier()
                ]);
            }

            // Code Validation succeeded
            $this->manager->markVerificationAsCompleted($verification);

            $this->messageManager->addSuccessMessage(
                __('Authentication successfully completed')
            );

            return $resultRedirect->setPath('*/*/result', [
                'id' => $verification->getCustomerIdentifier()
            ]);

        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(
                $exception->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An unspecified error occurred. Please contact us for assistance.')
            );
        }

        return $resultRedirect->setPath('*/*/form', ['id' => $idParam]);
    }

    /**
     * Validates form key
     *
     * @return void
     * @throws LocalizedException
     */
    private function validateFormKey()
    {
        if (!$this->formKeyValidator->validate($this->request)) {
            throw new LocalizedException(
                __('Something went wrong while saving the page. Please refresh the page and try again.')
            );
        }
    }
}
