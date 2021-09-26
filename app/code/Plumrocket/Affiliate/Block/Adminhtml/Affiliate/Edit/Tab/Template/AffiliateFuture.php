<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class AffiliateFuture extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        /**
         * @var \Plumrocket\Affiliate\model\Affiliate\AffiliateFuture $affiliate
         */
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
                'label'    => 'Merchant Id',
                'required' => true,
                'class'    => 'validate-digits',
                'value'    => $affiliate->getMerchantId(),
                'note'     => 'Place the applicable AffiliateFuture Merchant ID here. ' .
                    'Provided to you when we created your account.',
            ]
        );

        $fieldset->addField(
            'additional_data_cps_enable',
            'select',
            [
                'name'      => 'additional_data[cps_enable]',
                'label'     => __('Pay-Per-Sale Program'),
                'value'     => $affiliate->getCpsEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.
                <style>
                    .admin__scope-old .form-inline .fieldset>.field>.label span {word-break: normal;}
                </style>
                ',
            ]
        );

        $fieldset->addField(
            'additional_data_cpl_enable',
            'select',
            [
                'name'      => 'additional_data[cpl_enable]',
                'label'     => __('Pay-Per-Lead Program'),
                'value'     => $affiliate->getCplEnabled(),
                'values'    => [
                    0 => __('Disabled'),
                    1 => __('Enabled'),
                ],
                'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a ' .
                    'fixed amount for each visitor referred by affiliate who sign up as lead ' .
                    '(registers an account on Merchant\'s site). PPL campaigns are suitable ' .
                    'for building a newsletter list, member acquisition program or reward program.',
            ]
        );

        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'  => 'section_bodybegin_includeon_id',
                'value' => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'  => 'section_bodyend_includeon_id',
                'value' => $this->getIncludeonByKey('registration_success_pages')->getId(),
            ]
        );

        return $this;
    }
}
