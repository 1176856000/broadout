<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab;

use Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Types
     * @var \Plumrocket\Affiliate\Model\ResourceModel\Type\Collection
     */
    protected $_types;

    /**
     * @var \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface
     */
    protected $_currentType;

    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface
     */
    private $affiliateProgramTypeProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Framework\Data\FormFactory                             $formFactory
     * @param \Magento\Store\Model\System\Store                               $systemStore
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        AffiliateProgramTypeProviderInterface $affiliateProgramTypeProvider,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->affiliateProgramTypeProvider = $affiliateProgramTypeProvider;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $model = $this->getAffiliate();
        $type = $this->getType();

        $form->setHtmlIdPrefix('affiliate_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => $model->getId() ? __('Edit Affiliate Program') : __('Add New Affiliate Program')]
        );

        $isElementDisabled = false;

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                [
                    'name'      => 'id',
                    'value'     => $model->getId() ?: null,
                ]
            );
        }

        $fieldset->addField(
            'type_id',
            'hidden',
            [
                'name'      => 'type_id',
                'value'     => $model->getTypeId() ?: $type->getId(),
            ]
        );

        $fieldset->addField(
            'network',
            'note',
            [
                'name' => 'network',
                'label' => __('Affiliate Network'),
                'title' => __('Affiliate Network'),
                'text' => $this->getNetworkLabel($model)
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'value' => $model->getData('name') ?: $type->getName(),
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => \Plumrocket\Affiliate\Model\Affiliate::getAvailableStatuses(),
                'disabled' => $isElementDisabled,
                'value' => $model->getStatus() ?? 1,
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {

            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
                    'value'     => $model->getStores()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $this->_eventManager->dispatch('plumrocket_affiliate_edit_tab_main_prepare_form', ['form' => $form]);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $model
     * @return \Magento\Framework\Phrase|string
     */
    public function getNetworkLabel($model)
    {
        $logo = $this->getType()->getLogo();
        if (! $logo) {
            return __($this->getType()->getName());
        }

        return '<img src="' . $this->getViewFileUrl($logo) . '" />';
    }

    /**
     * get current model
     */
    public function getAffiliate()
    {
        return $this->_coreRegistry->registry('current_model');
    }

    /**
     * @return \Plumrocket\Affiliate\Api\Data\AffiliateProgramTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getType()
    {
        $typeId = $this->getAffiliate()->getTypeId() ?: $this->getRequest()->getParam('type_id');
        if ($this->_currentType === null) {
            $this->_currentType = $this->affiliateProgramTypeProvider->getById((int) $typeId);
        }
        return $this->_currentType;
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
