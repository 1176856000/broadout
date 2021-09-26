<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class AvantLink extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();
        $fieldset = $form->addFieldset('section_bodyend', ['legend' => __('Affiliate Script - Pay Per Sale (PPS) or Cost Per Sale (CPS) Program'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_site_id',
            'text',
            [
                'name'      => 'additional_data[site_id]',
                'label'     => 'Site ID',
                'required'  => true,
                'value'     => $affiliate->getSiteId(),
                'note' => 'Appropriate value for your site. Contact AvantlLink support if you need help obtaining this value.',
            ]
        );

        $fieldset->addField(
            'section_bodyend_includeon_id',
            'hidden',
            [
                'name'      => 'section_bodyend_includeon_id',
                'value'     => $this->getIncludeonByKey()->getId(),
            ]
        );

        return $this;
    }
}
