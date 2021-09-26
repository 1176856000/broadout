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

class LoadUrl extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $helperJson;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $helperJson,
        \Magento\Store\Model\StoreManager $storeManager
    ) {
        parent::__construct($context);
        $this->helperJson   = $helperJson;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $storeId = $this->_request->getParam('store_id');

        if ($storeId !== null) {
            $url = $this->storeManager
                    ->getStore()
                    ->getUrl('', ['_nosid' => true]);

            $this->helperJson->jsonEncode(['success' => true, 'url' => $url]);
        } else {
            $this->helperJson->jsonEncode(['success' => false, 'error' => __('Store id is missing')]);
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Plumrocket_Datagenerator::prdatagenerator');
    }
}
