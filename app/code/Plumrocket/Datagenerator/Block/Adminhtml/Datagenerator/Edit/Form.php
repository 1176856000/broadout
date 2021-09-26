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

namespace Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Store manager
     * * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * Backend helper
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * {@inheritdoc}
     */
    protected $_formId = 'edit_form';

    /**
     * @param \Magento\Store\Model\StoreManager       $storeManager
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Backend\Helper\Data            $backendHelper,
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param Array                                   $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => $this->_formId, 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        return $html . $this->_getJs();
    }

    /**
     * Get js
     * @return string
     */
    protected function _getJs()
    {
        return "<script type='text/javascript'>
            require(['jquery', 'mage/mage', 'domReady!'], function($){
                $('#".$this->_formId."').mage('prDatageneratorEdit', " . $this->_getJsOptions() . ");
            });
        </script>";
    }

    /**
     * Retrieve options for js script
     * @return string
     */
    protected function _getJsOptions()
    {

        $params = [
            'tempateAction' => $this->getUrl('prdatagenerator/datagenerator/loadTemplate'),
            'storeViews' => $this->_getStoreViews()
            ];

        return json_encode($params);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getStoreViews()
    {
        $defaultStoreId = $this->_storeManager
                ->getWebsite(null)
                ->getDefaultGroup()
                ->getDefaultStoreId();

        $url = $this->_storeManager
                    ->getStore($defaultStoreId)
                    ->getBaseUrl();

        $stores = [0 => $url];
        foreach ($this->_storeManager->getStores() as $store) {
            $stores[$store->getId()] = $store->getBaseUrl();
        }

        return $stores;
    }
}
