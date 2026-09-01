<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;

/**
 * Abstract class for Configuration Template section
 */
abstract class Configuration extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Line_VerifiedPurchase::marketing_email';

    /**
     * Init action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        $this->_setActiveMenu(
            self::ADMIN_RESOURCE
        )->_addBreadcrumb(
            __('Verified Purchase'),
            __('Verified Purchase')
        );

        return $this;
    }
}
