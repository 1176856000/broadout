<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class WebGains extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_program_id',
            'text',
            [
                'name'      => 'additional_data[program_id]',
                'label'     => 'Program ID',
                'required'  => true,
                'value'     => $affiliate->getProgramId(),
                'note' => 'A static program ID constant provided to you by WebGains.',
            ]
        );

        $fieldset->addField(
            'additional_data_event_id',
            'text',
            [
                'name'      => 'additional_data[event_id]',
                'label'     => 'Event ID',
                'required'  => true,
                'value'     => $affiliate->getEventId(),
                'note' => 'This identifies the commission type (in account under Program Setup (commission types).',
            ]
        );

        $fieldset->addField(
            'additional_data_pin_id',
            'text',
            [
                'name'      => 'additional_data[pin_id]',
                'label'     => 'Pin Number',
                'required'  => true,
                'value'     => $affiliate->getPin(),
                'note' => 'Pin number provided by WebGains (in account under Program Setup (program settings -> technical setup)).',
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
