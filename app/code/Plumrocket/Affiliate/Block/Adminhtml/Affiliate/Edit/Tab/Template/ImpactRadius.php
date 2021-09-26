<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class ImpactRadius extends AbstractNetwork
{

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_tracking_domain',
            'text',
            [
                'name'      => 'additional_data[tracking_domain]',
                'label'     => 'Tracking Domain',
                'required'  => true,
                'value'     => $affiliate->getTrackingDomain(),
                'note'      => 'The tracking domain as specified by the advertiser.',
            ]
        );

        $fieldset->addField(
            'additional_data_campaign_id',
            'text',
            [
                'name'      => 'additional_data[campaign_id]',
                'label'     => 'Campaign ID',
                'required'  => true,
                'class'     => 'validate-digits',
                'value'     => $affiliate->getCampaignId(),
                'note'      => 'Uniquely identifies your campaign.',
            ]
        );

        $fieldset->addField(
            'additional_data_tracking_action_id',
            'text',
            [
                'name'      => 'additional_data[tracking_action_id]',
                'label'     => 'Action Tracker ID',
                'required'  => true,
                'class'     => 'validate-digits',
                'value'     => $affiliate->getTrackingActionId(),
                'note'      => 'Uniquely identifies your action tracker.',
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
                'note' => 'Pay Per Sale (PPS) or Cost Per Sale (CPS). Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something. Merchant only pays its affiliates when it gets a desired result.',
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
                'note' => 'Pay Per Lead (PPL) or Cost Per Lead (CPL). Merchant site pays a fixed amount for each visitor referred by affiliate who sign up as lead (registers an account on Merchant\'s site). PPL campaigns are suitable for building a newsletter list, member acquisition program or reward program.',
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getIncludeonByKey('all')->getId(),
            ]
        );

        return $this;
    }
}
