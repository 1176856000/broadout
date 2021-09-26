<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class RoiTracker extends AbstractNetwork
{
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
                'name'      => 'additional_data[merchant_id]',
                'label'     => 'Merchant ID',
                'required'  => true,
                'value'     => $affiliate->getMerchantId(),
                'note' => 'The merchant’s ID number with ShareASale',
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
