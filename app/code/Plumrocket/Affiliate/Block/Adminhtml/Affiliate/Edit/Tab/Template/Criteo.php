<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Criteo extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset(
            'section_bodyend',
            ['legend' => __('Affiliate Script'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'additional_data_partner_id',
            'text',
            [
                'name'      => 'additional_data[partner_id]',
                'label'     => 'Criteo Partner ID',
                'required'  => true,
                'class'     => 'validate-digits input-text-short',
                'value'     => $affiliate->getPartnerId(),
                'note'      => 'If you\'re unaware of this value, please contact Criteo.',
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
