<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;

class LoadTemplate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $helperJson;

    /**
     * @var \Plumrocket\Datagenerator\Model\TemplateFactory
     */
    protected $templateFactory;

    /**
     * LoadTemplate constructor.
     *
     * @param \Magento\Backend\App\Action\Context             $context
     * @param \Magento\Framework\Json\Helper\Data             $helperJson
     * @param \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $helperJson,
        \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
    ) {
        parent::__construct($context);
        $this->helperJson       = $helperJson;
        $this->templateFactory  = $templateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $templateId = $this->_request->getParam('template_id');

        if (!$templateId) {
            return $this->getResponse()->representJson(
                $this->helperJson->jsonEncode(['error' => __('No Request Template Id')])
            );
        }

        $template = $this->templateFactory->create()->load($templateId);

        if (!$template->getId()) {
            return $this->getResponse()->representJson(
                $this->helperJson->jsonEncode(['error' => __('Can not load template')])
            );
        }

        return $this->getResponse()->representJson(
            $this->helperJson->jsonEncode($template->getData())
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Plumrocket_Datagenerator::prdatagenerator');
    }
}
