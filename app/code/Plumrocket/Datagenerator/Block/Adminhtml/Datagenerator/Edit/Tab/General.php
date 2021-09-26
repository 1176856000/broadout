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

namespace Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{
    /**
     * Template source
     * @var \Plumrocket\Datagenerator\Model\Config\Source\Template
     */
    protected $_templateSource;

    /**
     * Type source
     * @var \Plumrocket\Datagenerator\Model\Config\Source\Type
     */
    protected $_typeSource;

    /**
     * System store
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Store manager
     * @var  \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * Backend helper
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * General constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Plumrocket\Datagenerator\Model\Config\Source\Template $templateSource
     * @param \Plumrocket\Datagenerator\Model\Config\Source\Type $typeSource
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\Datagenerator\Model\Config\Source\Template $templateSource,
        \Plumrocket\Datagenerator\Model\Config\Source\Type $typeSource,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_templateSource = $templateSource;
        $this->_systemStore = $systemStore;
        $this->_storeManager = $storeManager;
        $this->_typeSource = $typeSource;
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('current_model');

        /*
         * Checking if user have permissions to save information
         */
        $form->setHtmlIdPrefix('datagenerator_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => 'Add New Data Feed']);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'template_id',
            'select',
            [
                'name' => 'template_id',
                'label' => __('Select Template'),
                'title' => __('Select Template'),
                'required' => false,
                'values' => $this->_getTemplates()
            ]
        )
        ->setAfterElementHtml($this->_getLoaderHtml());

        $fieldset->addField(
            'type_feed',
            'select',
            [
                'name' => 'type_feed',
                'label' => __('Data Feed Type'),
                'title' => __('Data Feed Type'),
                'required' => true,
                'disabled' => ($model->getTemplateId()) ? 1 : 0,
                'values' => $this->_typeSource->toOptionArray()
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'required' => true,
                'note' => $this->_getUrlKeyNoteHtml($model->getUrlKey())
            ]
        );

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name' => 'store_id',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ]
        );

        $fieldset->addField(
            'count',
            'text',
            [
                'name' => 'count',
                'label' => __('Number of Items in Feed'),
                'title' => __('Number of Items in Feed'),
                'note' => __('leave "0" or empty if unlimited')
            ]
        );

        $fieldset->addField(
            'cache_time',
            'text',
            [
                'name' => 'cache_time',
                'label' => __('Cache Life Time (seconds)'),
                'title' => __('Cache Life Time (seconds)'),
                'note' => __('Set cache life time to "0" to disable caching of this data feed')
            ]
        );

        $fieldset->addField(
            'enabled',
            'select',
            [
                'name' => 'enabled',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => \Plumrocket\Datagenerator\Model\Template::getAvailableStatuses()
            ]
        );

        $this->_eventManager->dispatch('plumrocket_datagenerator_edit_tab_main_prepare_form', ['form' => $form]);

        $data = $model->getData();
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve url key comment html
     * @param string $address
     * @param int $storeId
     * @return string
     */
    protected function _getUrlKeyNoteHtml($address = '', $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager
                ->getWebsite(null)
                ->getDefaultGroup()
                ->getDefaultStoreId();
        }

        $url = $this->_storeManager
            ->getStore($storeId)
            ->getBaseUrl();

        return $this->_decorateUrlKey($url, $address);
    }

    /**
     * Decorate url key
     * @param string $url
     * @param string $address
     * @return string decorated html code
     */
    protected function _decorateUrlKey($url, $address = '')
    {
        if ($url === null) {
            return '';
        }

        $html = '<span class="base-url">' . $url . '</span>';
        $html .= \Plumrocket\Datagenerator\Helper\Data::$routeName . '/index/index/address/'
            . '<span class="address">' . $address . '</span>';

        return $html;
    }

    /**
     * Retrieve loader html
     * @return string
     */
    protected function _getLoaderHtml()
    {
        return '<img src="' . $this->getViewFileUrl('Plumrocket_Datagenerator::images/ajax-loader.gif')
            . '" id="template-loader" style="display:none;" />';
    }

    /**
     * Retrieve available templates
     * @return array
     */
    protected function _getTemplates()
    {
        return $this->_templateSource->toOptionArray();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
