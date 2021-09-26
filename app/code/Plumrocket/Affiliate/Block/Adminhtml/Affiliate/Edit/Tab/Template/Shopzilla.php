<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Shopzilla extends AbstractNetwork
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
                'note' => 'A static numeric merchant ID constant provided to you by Shopzilla.',
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
