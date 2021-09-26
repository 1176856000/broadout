<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class PerformanceHorizon extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_campaign_id',
            'text',
            [
                'name'      => 'additional_data[campaign_id]',
                'label'     => 'Campaign ID',
                'required'  => true,
                'value'     => $affiliate->getCampaignId(),
                'note' => 'The id of the campaign provided to you by PerformanceHorizon (format XXlXX).',
            ]
        );


        $fieldset->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        return $this;
    }
}
