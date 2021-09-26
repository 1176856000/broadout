<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

use Plumrocket\Affiliate\Api\Data\PageTypeInterface;

class AffiliateWindow extends AbstractNetwork
{
    /**
     * @var string
     */
    protected $paramKey = 'source';

    /**
     * @var string
     */
    protected $defaultValue = 'aw';

    /**
     * @var int
     */
    protected $cookieLength = 30;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\AffiliateWindow\CommissionGroup
     */
    protected $commissionGroup;

    /**
     * @param \Magento\Backend\Block\Template\Context                                   $context
     * @param \Plumrocket\Affiliate\Api\PageTypeProviderInterface                       $pageTypeProvider
     * @param \Magento\Config\Model\Config\Source\Yesno                                 $sourceYesno
     * @param \Plumrocket\Affiliate\Model\Config\Source\AffiliateWindow\CommissionGroup $commissionGroup
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context                                     $context,
        \Plumrocket\Affiliate\Api\PageTypeProviderInterface                         $pageTypeProvider,
        \Magento\Config\Model\Config\Source\Yesno                                   $sourceYesno,
        \Plumrocket\Affiliate\Model\Config\Source\AffiliateWindow\CommissionGroup   $commissionGroup,
        array $data = []
    ) {
        parent::__construct($context, $pageTypeProvider, $data);
        $this->sourceYesno          = $sourceYesno;
        $this->commissionGroup      = $commissionGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset(
            'section_bodybegin',
            ['legend' => __('Program Specific'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'additional_data_advertiser_id',
            'text',
            [
                'name'      => 'additional_data[advertiser_id]',
                'label'     => __('Merchant ID'),
                'required'  => true,
                'class'     => 'validate-digits',
                'value'     => $affiliate->getAdvertiserId(),
                'note'      => __('Please enter your merchant ID.'),
            ]
        );

        $fieldset->addField(
            'additional_data_delivery_cost_inclusive',
            'select',
            [
                'name'      => 'additional_data[delivery_cost_inclusive]',
                'label'     => __('Include Delivery Cost'),
                'value'     => $affiliate->getAdditionalDataValue('delivery_cost_inclusive'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Include delivery cost in total amount.'),
            ]
        );

        $fieldset->addField(
            'additional_data_tax_inclusive',
            'select',
            [
                'name'      => 'additional_data[tax_inclusive]',
                'label'     => __('Commission Includes VAT/Tax'),
                'value'     => $affiliate->getAdditionalDataValue('tax_inclusive'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('This setting is established during the integration period.'),
            ]
        );

        $fieldset->addField(
            'additional_data_taxes_inclusive',
            'select',
            [
                'name'      => 'additional_data[taxes_inclusive]',
                'label'     => __('Include Taxes'),
                'value'     => $affiliate->getAdditionalDataValue('taxes_inclusive'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Include taxes in total amount.'),
            ]
        );

        $fieldset->addField(
            'additional_data_commission_group',
            'select',
            [
                'name'      => 'additional_data[commission_group]',
                'label'     => __('Set Commission by'),
                'value'     => $affiliate->getAdditionalDataValue('commission_group'),
                'values'    => $this->commissionGroup->toOptionArray(),
                'note'      => __('Set comission type by product or customer.'),
            ]
        );

        $fieldsetGeneral = $form->addFieldset(
            'section_general',
            ['legend' => __('General Settings'), 'class' => 'fieldset-wide']
        );

        $fieldsetGeneral->addField(
            'additional_data_activate_tracking_code',
            'select',
            [
                'name'      => 'additional_data[activate_tracking_code]',
                'label'     => __('Activate Tracking Code'),
                'value'     => $affiliate->getAdditionalDataValue('activate_tracking_code'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Enable conversion tag.'),
            ]
        );

        $fieldsetGeneral->addField(
            'additional_data_plt',
            'select',
            [
                'name'      => 'additional_data[plt]',
                'label'     => __('Product Level Tracking'),
                'value'     => $affiliate->getAdditionalDataValue('plt'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Activate advanced product analytics.'),
            ]
        );

        $fieldsetGeneral->addField(
            'additional_data_test_mode',
            'select',
            [
                'name'      => 'additional_data[test_mode]',
                'label'     => __('In Test Mode'),
                'value'     => $affiliate->getAdditionalDataValue('test_mode'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Test enabled transactions cannot be validated.'),
            ]
        );

        $fieldsetdeduplication = $form->addFieldset(
            'section_deduplication',
            ['legend' => __('De-duplication'), 'class' => 'fieldset-wide']
        );

        $fieldsetdeduplication->addField(
            'additional_data_enable_dedupe',
            'select',
            [
                'name'      => 'additional_data[enable_dedupe]',
                'label'     => __('Enable De-duplication'),
                'value'     => $affiliate->getAdditionalDataValue('enable_dedupe'),
                'values'    => $this->sourceYesno->toOptionArray(),
                'note'      => __('Enable de-duplication logic.'),
            ]
        );

        $fieldsetdeduplication->addField(
            'additional_data_param_key',
            'text',
            [
                'name'      => 'additional_data[param_key]',
                'label'     => __('Key Parameter'),
                'value'     => ($affiliate->getAdditionalDataValue('param_key') ?: $this->paramKey),
                'note'      => __('The name of the parameter appended to clickthroughs to identify source.'),
            ]
        );

        $fieldsetdeduplication->addField(
            'additional_data_default_value',
            'text',
            [
                'name'      => 'additional_data[default_value]',
                'label'     => __('Default Value'),
                'value'     => ($affiliate->getAdditionalDataValue('default_value') ?: $this->defaultValue),
                'note'      => __('The default value if no source parameter is provided at clickthrough.'),
            ]
        );

        $fieldsetdeduplication->addField(
            'additional_data_cookie_length',
            'text',
            [
                'name'      => 'additional_data[cookie_length]',
                'label'     => __('Cookie Length'),
                'class'     => 'validate-digits',
                'value'     => ($affiliate->getAdditionalDataValue('cookie_length') ?: $this->cookieLength),
                'note'      => __('The length to set the source cookie in days. 30 is highly recommended.'),
            ]
        );

        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'value'     => $this->getPageType(PageTypeInterface::ANY)->getId(),
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getPageType(PageTypeInterface::ANY)->getId(),
            ]
        );

        return $this;
    }
}
