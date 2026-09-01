<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Controller\Adminhtml\Template\Configuration;

use Line\VerifiedPurchase\Controller\Adminhtml\Template\Configuration;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Index controller for template configuration section
 */
class Index extends Configuration implements HttpGetActionInterface
{
    /**
     * Template configuration main page
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()
            ->_addBreadcrumb(
                __('Marketing'),
                __('Marketing')
            );

        $this->_view->getPage()
            ->getConfig()
            ->getTitle()
            ->prepend(__('Verified Purchase (by Line)'));

        $this->_view->renderLayout();
    }
}
