<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Element\Dependence;

class Script extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * Main tab
     * @var Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Main
     */
    protected $_mainTab;

    /**
     * @var \Plumrocket\Affiliate\Model\AffiliateManager
     */
    protected $affiliateManager;
    /**
     * Current network
     * @var \Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template\AbstractNetwork
     */
    protected $_fieldsBlock;

    /**
     * @param \Magento\Backend\Block\Template\Context                       $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Main $mainTab
     * @param \Plumrocket\Affiliate\Model\AffiliateManager                  $affiliateManager
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context                         $context,
        \Magento\Framework\Registry                                     $registry,
        \Magento\Framework\Data\FormFactory                             $formFactory,
        \Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Main   $mainTab,
        \Plumrocket\Affiliate\Model\AffiliateManager                    $affiliateManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_mainTab = $mainTab;
        $this->affiliateManager = $affiliateManager;
    }

    /**
     * @return \Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template\AbstractNetwork
     */
    protected function _getFieldsBlock()
    {
        if ($this->_fieldsBlock === null) {
            $type = $this->_mainTab->getType();

            $this->_fieldsBlock = $this->affiliateManager->getAffiliateTemplateBlock($type);
            $this->_fieldsBlock
                ->setAffiliate($this->_mainTab->getAffiliate())
                ->setTab($this)
                ->setParentBlock($this);
        }
        return $this->_fieldsBlock;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('affiliate_');
        $this->_getFieldsBlock()->prepareForm($form);
        $pre = $form->getHtmlIdPrefix();
        switch ($this->_mainTab->getType()->getKey()) {
            case 'linkshare':
                $this->setChild(
                    'form_after',
                    $this->getLayout()->createBlock(Dependence::class)
                    ->addFieldMap($pre . 'additional_data_use_cadence', 'data_use_cadence')
                    ->addFieldMap($pre . 'additional_data_tracking_key', 'data_tracking_key')

                    ->addFieldDependence('data_tracking_key', 'data_use_cadence', '1')
                );
                break;

            case 'zanox':
                $this->setChild(
                    'form_after',
                    $this->getLayout()->createBlock(Dependence::class)

                    ->addFieldMap($pre . 'additional_data_cps_enable',           'data_cps_enable')
                    ->addFieldMap($pre . 'additional_data_cps_program_code_aid', 'data_cps_program_code_aid')
                    ->addFieldMap($pre . 'additional_data_home_aid',             'data_home_aid')
                    ->addFieldMap($pre . 'additional_data_category_aid',         'data_category_aid')
                    ->addFieldMap($pre . 'additional_data_product_aid',          'data_product_aid')
                    ->addFieldMap($pre . 'additional_data_cart_aid',             'data_cart_aid')
                    ->addFieldMap($pre . 'additional_data_checkout_success_aid', 'data_checkout_success_aid')
                    ->addFieldMap($pre . 'additional_data_generic_aid',          'data_generic_aid')

                    ->addFieldDependence('data_cps_program_code_aid', 'data_cps_enable', '1')
                    ->addFieldDependence('data_home_aid',             'data_cps_enable', '1')
                    ->addFieldDependence('data_category_aid',         'data_cps_enable', '1')
                    ->addFieldDependence('data_product_aid',          'data_cps_enable', '1')
                    ->addFieldDependence('data_cart_aid',             'data_cps_enable', '1')
                    ->addFieldDependence('data_checkout_success_aid', 'data_cps_enable', '1')
                    ->addFieldDependence('data_generic_aid',          'data_cps_enable', '1')

                    ->addFieldMap($pre . 'additional_data_cpl_enable',               'data_cpl_enable')
                    ->addFieldMap($pre . 'additional_data_cpl_program_code_aid',     'data_cpl_program_code_aid')
                    ->addFieldMap($pre . 'additional_data_registration_success_aid', 'data_registration_success_aid')

                    ->addFieldDependence('data_cpl_program_code_aid',     'data_cpl_enable', '1')
                    ->addFieldDependence('data_registration_success_aid', 'data_cpl_enable', '1')
                );
                break;

            case 'affiliateWindow':
                $this->setChild(
                    'form_after',
                    $this->getLayout()->createBlock(Dependence::class)

                    ->addFieldMap($pre . 'additional_data_activate_tracking_code',  'data_activate_tracking_code')
                    ->addFieldMap($pre . 'additional_data_plt',                     'data_plt')
                    ->addFieldMap($pre . 'additional_data_test_mode',               'data_test_mode')

                    ->addFieldDependence('data_plt',        'data_activate_tracking_code', '1')
                    ->addFieldDependence('data_test_mode',  'data_activate_tracking_code', '1')

                    ->addFieldMap($pre . 'additional_data_enable_dedupe',   'data_enable_dedupe')
                    ->addFieldMap($pre . 'additional_data_param_key',       'data_param_key')
                    ->addFieldMap($pre . 'additional_data_default_value',   'data_default_value')
                    ->addFieldMap($pre . 'additional_data_cookie_length',   'data_cookie_length')

                    ->addFieldDependence('data_param_key',      'data_enable_dedupe', '1')
                    ->addFieldDependence('data_default_value',  'data_enable_dedupe', '1')
                    ->addFieldDependence('data_cookie_length',  'data_enable_dedupe', '1')
                );
                break;
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        $label = $this->_getFieldsBlock()->getTabLabel();
        return $label ?: __('Affiliate Script');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        $label = $this->_getFieldsBlock()->getTabLabel();
        return $label ?: __('Affiliate Script');
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
