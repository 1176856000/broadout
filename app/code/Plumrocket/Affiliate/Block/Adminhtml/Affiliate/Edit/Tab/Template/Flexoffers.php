<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Flexoffers extends AbstractNetwork
{

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_advertiser_id',
            'text',
            [
                'name'      => 'additional_data[advertiser_id]',
                'label'     => 'Advertiser ID',
                'required'  => true,
                'value'     => $affiliate->getMerchantId(),
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
