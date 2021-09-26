<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Block\Adminhtml\Datagenerator;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Plumrocket_Datagenerator';
        $this->_controller = 'adminhtml_datagenerator';
        $currentTemplateId = $this->getRequest()->getParam('id');
        parent::_construct();

        if ($this->_isAllowedAction('Plumrocket_Datagenerator::prdatagenerator')) {
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );

            if ($currentTemplateId) {
                $this->buttonList->add(
                    'rebuild',
                    [
                        'label' => __('Rebuild Data Feed'),
                        'class' => 'primary',
                        'onclick' => 'setLocation(\''
                            . $this->getUrl('*/*/rebuild', ['id' => $currentTemplateId])
                            . '\')',
                    ],
                    -100
                );
            }
        } else {
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($model->getId()) {
            return __("Edit Datagenerator");
        } else {
            return __('New Datagenerator');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
}
