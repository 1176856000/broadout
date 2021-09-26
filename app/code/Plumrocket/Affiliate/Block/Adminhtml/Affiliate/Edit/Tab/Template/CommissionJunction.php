<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class CommissionJunction extends AbstractNetwork
{

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_merchant_id',
            'text',
            [
                'name'      => 'additional_data[merchant_id]',
                'label'     => 'Merchant ID',
                'required'  => true,
                'value'     => $affiliate->getMerchantId(),
                'note' => 'A static numeric merchant ID constant provided to you by Commission Junction.  <br/>Merchant site pays a percentage of the sale when the affiliate sends them a customer who purchases something.',
            ]
        );

        $fieldset->addField(
            'additional_data_merchant_type',
            'text',
            [
                'name'      => 'additional_data[merchant_type]',
                'label'     => 'Merchant TYPE',
                'required'  => true,
                'value'     => $affiliate->getMerchantType(),
                'note' => 'A static numeric merchant TYPE constant provided to you by Commission Junction.',
            ]
        );

        $fieldset->addField(
            'additional_data_container_tag_id',
            'text',
            [
                'name'      => 'additional_data[container_tag_id]',
                'label'     => 'Container Tag ID',
                'required'  => true,
                'value'     => $affiliate->getContainerTagId(),
                'note' => 'A static numeric provided to you by Commission Junction.',
            ]
        );

        $fieldset->addField(
            'section_bodybegin_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodybegin_includeon_id',
                'value'     => $this->getIncludeonByKey('checkout_success')->getId(),
            ]
        );

        return $this;
    }
}
