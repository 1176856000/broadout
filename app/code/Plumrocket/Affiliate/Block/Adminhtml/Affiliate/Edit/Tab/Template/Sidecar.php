<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Affiliate\Edit\Tab\Template;

class Sidecar extends AbstractNetwork
{

    /**
     * {@inheritdoc}
     */
    public function prepareForm($form)
    {
        $affiliate  = $this->getAffiliate();

        $fieldset = $form->addFieldset('section_bodybegin', ['legend' => $this->getTabLabel(), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'additional_data_sitename',
            'text',
            [
                'name'      => 'additional_data[sitename]',
                'label'     => 'Site Name',
                'required'  => true,
                'value'     => $affiliate->getSitename(),
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


    public function getTabLabel()
    {
        return __('Sidecar Information');
    }
}
