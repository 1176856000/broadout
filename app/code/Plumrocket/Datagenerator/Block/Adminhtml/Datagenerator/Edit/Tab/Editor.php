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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Editor extends Generic implements TabInterface
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
     * Data helper
     * @var \Plumrocket\Datagenerator\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Json helper
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Editor constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Plumrocket\Datagenerator\Model\Config\Source\Template $templateSource
     * @param \Plumrocket\Datagenerator\Model\Config\Source\Type $typeSource
     * @param \Plumrocket\Datagenerator\Helper\Data $dataHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\Datagenerator\Model\Config\Source\Template $templateSource,
        \Plumrocket\Datagenerator\Model\Config\Source\Type $typeSource,
        \Plumrocket\Datagenerator\Helper\Data $dataHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_templateSource = $templateSource;
        $this->_jsonHelper = $jsonHelper;
        $this->_systemStore = $systemStore;
        $this->_dataHelper = $dataHelper;
        $this->_typeSource = $typeSource;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve header code
     * @return string
     */
    public function getCodeHeader()
    {
        $model = $this->_coreRegistry->registry('current_model');
        return $model->getCodeHeader();
    }

    /**
     * Retrieve one item code
     * @return string
     */
    public function getCodeItem()
    {
        $model = $this->_coreRegistry->registry('current_model');
        return $model->getCodeItem();
    }

    /**
     * Retrieve code footer
     * @return string
     */
    public function getCodeFooter()
    {
        $model = $this->_coreRegistry->registry('current_model');
        return $model->getCodeFooter();
    }

    /**
     * Retrieve attributes
     * @return string
     */
    public function getAttributes()
    {
        $data = $this->_dataHelper->getAttributes();
        return $this->_jsonHelper->jsonEncode($data);
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Template Editor');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Template Editor');
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
