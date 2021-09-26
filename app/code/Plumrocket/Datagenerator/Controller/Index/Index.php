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

namespace Plumrocket\Datagenerator\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Data helper
     * @var \Plumrocket\Datagenerator\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Tempalte factory
     * @var \Plumrocket\Datagenerator\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Request interface
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Render model
     * @var \Plumrocket\Datagenerator\Model\Render
     */
    protected $_renderModel;

    /**
     * Layout factory
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @param \Magento\Framework\App\Action\Context           $context
     * @param \Plumrocket\Datagenerator\Helper\Data           $dataHelper
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager
     * @param \Magento\Framework\View\LayoutFactory           $layoutFactory
     * @param \Plumrocket\Datagenerator\Model\Render          $renderModel
     * @param \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\Datagenerator\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Plumrocket\Datagenerator\Model\Render $renderModel,
        \Plumrocket\Datagenerator\Model\TemplateFactory $templateFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_templateFactory = $templateFactory;
        $this->_renderModel = $renderModel;
        $this->_layoutFactory = $layoutFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        error_reporting(E_ALL);
        set_time_limit(0); // phpcs:ignore

        if ($this->_dataHelper->moduleEnabled()) {
            $urlKey = $this->_request->getParam('address');

            $template = $this->_templateFactory->create()
                ->getCollection()
                ->addFieldToFilter('enabled', \Plumrocket\Datagenerator\Model\Template::STATUS_ENABLED)
                ->addFieldToFilter('type_entity', \Plumrocket\Datagenerator\Model\Template::ENTITY_TYPE_FEED)
                ->addFieldToFilter('url_key', $urlKey)
                ->getFirstItem();

            if ($template->getId()) {
                if ($template->getStoreId() > 0) {
                    $this->_storeManager->setCurrentStore($template->getStoreId());
                }

                $renderModel = $this->_renderModel->setTemplate($template);
                $text = $renderModel->getText();

                $contentType = 'text/html';
                if ($this->_request->getParam('no_output') !== 'yes') {
                    $ext = $template->getExt();
                    if ($ext == 'csv') {
                        $contentType = 'text/csv';
                    } elseif (($ext == 'xml') || ($ext == 'rss') || ($ext == 'atom')) {
                        $contentType = 'application/xml';
                    }
                } else {
                    $text = 'OK';
                }

                $this->_layoutFactory->create(['cacheable' => false]);

                $this->getResponse()
                    ->setHeader('Content-Type', $contentType)
                    ->setBody($text);

            } else {
                $this->_forward('noRoute');
            }
        } else {
            $this->_forward('noRoute');
        }
    }
}
