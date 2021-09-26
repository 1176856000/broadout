<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Pepperjam extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        // PepperJam.
        $fieldset = $form->addFieldset('section_bodyend', ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_program_id',
            'text',
            [
                'name'      => 'additional_data[program_id]',
                'label'     => 'Program ID',
                'required'  => true,
                'class'     => 'validate-digits input-text-short',
                'value'     => $affiliate->getProgramId(),
                'note'      => 'A static numeric program ID constant provided to you by PepperJam.',
            ]
        );

        $fieldset->addField(
            'additional_data_tracking_type',
            'select',
            [
                'name'      => 'additional_data[tracking_type]',
                'label'     => __('Tracking Integration Type'),
                'value'     => $affiliate->getTrackingType() ? $affiliate->getTrackingType() : 'itemized',
                'values'    => [
                    'basic'     => __('Basic'),
                    'itemized'  => __('Itemized'),
                    'dynamic'   => __('Dynamic'),
                ],
                'note' => 'Please select your type of tracking integration. Your affiliate program relies on a correctly working pixel, which allows Pepperjam to receive details of all affiliate-referred transactions that occur on your site.',
            ]
        );

        $fieldset->addField(
            'additional_data_transaction_type',
            'select',
            [
                'name'      => 'additional_data[transaction_type]',
                'label'     => __('Transaction Type'),
                'value'     => $affiliate->getTransactionType() ? $affiliate->getTransactionType() : 1,
                'values'    => [
                    1 => __('Sale'),
                    2 => __('Lead'),
                ],
                'note' => 'Please select transaction type. Used for Basic Tracking Integration only.',
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
