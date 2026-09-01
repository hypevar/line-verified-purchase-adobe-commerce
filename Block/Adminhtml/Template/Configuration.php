<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Block\Adminhtml\Template;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Item;
use Magento\Backend\Block\Widget\Button\ToolbarInterface;
use Magento\Backend\Block\Widget\ContainerInterface;
use Magento\Config\Model\Config\Source\Email\Template;

class Configuration extends Widget implements ContainerInterface
{
    /**
     * @var ButtonList
     */
    protected $buttonList;

    /**
     * @var ToolbarInterface
     */
    protected ToolbarInterface $toolbar;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var Template
     */
    protected Template $template;

    /**#@+
     * @var string
     * @access private
     */
    private const XPATH_BASE = ConfigInterface::XPATH_BASE;
    private const XPATH_PENDING_TEMPLATE = ConfigInterface::XPATH_EMAIL_TEMPLATE_PENDING;
    private const XPATH_COMPLETED_TEMPLATE = ConfigInterface::XPATH_EMAIL_TEMPLATE_COMPLETED;
    private const XPATH_CANCELED_TEMPLATE = ConfigInterface::XPATH_EMAIL_TEMPLATE_CANCELED;
    /**#@-*/

    /**
     * @param Context $context
     * @param ButtonList $buttonList
     * @param ToolbarInterface $toolbar
     * @param ConfigInterface $config
     * @param Template $template
     * @param array $data
     */
    public function __construct(
        Context $context,
        ButtonList $buttonList,
        ToolbarInterface $toolbar,
        ConfigInterface $config,
        Template $template,
        array $data = []
    ) {
        $this->buttonList = $buttonList;
        $this->toolbar = $toolbar;
        $this->config = $config;
        $this->template = $template;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function addButton(
        $buttonId,
        $data,
        $level = 0,
        $sortOrder = 0,
        $region = 'toolbar'
    ) {
        $this->buttonList->add($buttonId, $data, $level, $sortOrder, $region);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function updateButton($buttonId, $key, $data)
    {
        $this->buttonList->update($buttonId, $key, $data);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function canRender(Item $item)
    {
        return !$item->isDeleted();
    }

    /**
     * @inheritdoc
     */
    public function removeButton($buttonId)
    {
        $this->buttonList->remove($buttonId);
        return $this;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'save',
            [
                'label' => __('Save'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#template_configuration_form'
                        ],
                    ],
                ],
                'class' => 'save primary'
            ]
        );

        $this->toolbar->pushButtons($this, $this->buttonList);

        return parent::_prepareLayout();
    }

    /**
     * Get templates for Pending Notification
     *
     * @return array
     */
    public function getPendingTemplates()
    {
        return $this->template
            ->setPath(self::XPATH_BASE . self::XPATH_PENDING_TEMPLATE)
            ->toOptionArray();
    }

    /**
     * Get templates for Completed Notification
     *
     * @return array
     */
    public function getCompletedTemplates()
    {
        return $this->template
            ->setPath(self::XPATH_BASE . self::XPATH_COMPLETED_TEMPLATE)
            ->toOptionArray();
    }

    /**
     * Get templates for Canceled Notification
     *
     * @return array
     */
    public function getCanceledTemplates()
    {
        return $this->template
            ->setPath(self::XPATH_BASE . self::XPATH_CANCELED_TEMPLATE)
            ->toOptionArray();
    }

    /**
     * Return current Pending template
     *
     * @return string|null
     */
    public function getCurrentPendingTemplate(): ?string
    {
        return $this->config->getEmailProcessPendingTemplate();
    }

    /**
     * Return current Completed template
     *
     * @return string|null
     */
    public function getCurrentCompletedTemplate(): ?string
    {
        return $this->config->getEmailProcessCompleteTemplate();
    }

    /**
     * Return current Canceled template
     *
     * @return string|null
     */
    public function getCurrentCanceledTemplate(): ?string
    {
        return $this->config->getEmailProcessCanceledTemplate();
    }

    /**
     * Form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
