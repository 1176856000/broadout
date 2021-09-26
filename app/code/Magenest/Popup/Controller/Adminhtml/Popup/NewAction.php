<?php
namespace Magenest\Popup\Controller\Adminhtml\Popup;

/**
 * Class NewAction
 * @package Magenest\Popup\Controller\Adminhtml\Popup
 */
class NewAction extends \Magento\Backend\App\Action {
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}