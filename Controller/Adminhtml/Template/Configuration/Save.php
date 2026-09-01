<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

namespace Line\VerifiedPurchase\Controller\Adminhtml\Template\Configuration;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\Redirect;

class Save extends Action
{
    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var WriterInterface
     */
    protected WriterInterface $writer;

    /**
     * @var TypeListInterface
     */
    protected TypeListInterface $cacheTypeList;

    /**
     * Class constructor
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList
    ) {
        $this->config = $config;
        $this->writer = $configWriter;
        $this->cacheTypeList = $cacheTypeList;

        parent::__construct($context);
    }

    /**
     * Provides content
     *
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $templates = $this->getRequest()->getParam('templates');

        $pending = $templates['pending'];
        $completed = $templates['completed'];
        $canceled = $templates['canceled'];

        $baseXPATH = ConfigInterface::XPATH_BASE;
        $pendingXpath = $baseXPATH . ConfigInterface::XPATH_EMAIL_TEMPLATE_PENDING;
        $completedXpath = $baseXPATH . ConfigInterface::XPATH_EMAIL_TEMPLATE_COMPLETED;
        $canceledXpath = $baseXPATH . ConfigInterface::XPATH_EMAIL_TEMPLATE_CANCELED;

        $oldPending = $this->config->getEmailProcessPendingTemplate();
        $oldCompleted = $this->config->getEmailProcessCompleteTemplate();
        $oldCanceled = $this->config->getEmailProcessCanceledTemplate();

        // ensure that at least one value has changed
        if ($oldPending === $pending
            && $oldCompleted === $completed
            && $oldCanceled === $canceled
        ) {
            // $this->messageManager->addNoticeMessage(__('No template changes were detected'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            if ($oldPending !== $pending) {
                $this->writer->save($pendingXpath, $pending);
            }

            if ($oldCompleted !== $completed) {
                $this->writer->save($completedXpath, $completed);
            }

            if ($oldCanceled !== $canceled) {
                $this->writer->save($canceledXpath, $canceled);
            }

            // invalidate Configuration Cachce
            $this->cacheTypeList->invalidate(Config::TYPE_IDENTIFIER);

            $this->messageManager->addSuccessMessage(
                __('Configuration updated successfully.')
            );
            $this->messageManager->addNoticeMessage(
                __('Changes wont be reflected till you refresh Configuration Cache')
            );

        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__($exception->getMessage()));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check Authorization
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Line_VerifiedPurchase::template_configuration');
    }
}
