<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Shareasale extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset(
            'section_bodybegin',
            ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'additional_data_merchant_id',
            'text',
            [
                'name'     => 'additional_data[merchant_id]',
                'label'    => 'Merchant ID',
                'required' => true,
                'value'    => $affiliate->getMerchantId(),
                'note'     => 'The merchant’s ID number with ShareASale',
            ]
        );

        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'select',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'label'     => __('Pay-Per-Sale Program'),
                'value'     => $affiliate->getSectionBodybeginIncludeonId(),
                'values'    => [
                    0 => __('Disabled'),
                    $this->getIncludeonByKey('checkout_success')->getId() => __('Enabled'),
                ],
                'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the ' .
                    'sale when the affiliate sends them a customer who purchases something. ' .
                    'Merchant only pays its affiliates when it gets a desired result.',
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'select',
            [
                'name'      => 'section_bodyend_includeon_id',
                'label'     => __('Pay-Per-Lead Program'),
                'value'     => $affiliate->getSectionBodyendIncludeonId(),
                'values'    => [
                    0 => __('Disabled'),
                    $this->getIncludeonByKey('registration_success_pages')->getId() => __('Enabled'),
                ],
                'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount ' .
                    'for each visitor referred by affiliate who sign up as lead (registers an account on ' .
                    'Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member ' .
                    'acquisition program or reward program.',
            ]
        );

        $fieldset->addField(
            'additional_data_storeid',
            'text',
            [
                'name'     => 'additional_data[storeid]',
                'label'    => 'Store ID',
                'required' => true,
                'value'    => $affiliate->getStoreid(),
                'note'     => 'Store ID for StoresConnect feature',
            ]
        );

        return $this;
    }
}
