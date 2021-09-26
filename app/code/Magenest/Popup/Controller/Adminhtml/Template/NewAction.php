<?php
namespace Magenest\Popup\Controller\Adminhtml\Template;

/**
 * Class NewAction
 * @package Magenest\Popup\Controller\Adminhtml\Template
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