<?php
namespace Magenest\Popup\Controller\Adminhtml\Log;

/**
 * Class Index
 * @package Magenest\Popup\Controller\Adminhtml\Log
 */
class Index extends \Magento\Backend\App\Action {

    /** @var  \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Data Log'), __('Data Log'));
        $resultPage->getConfig()->getTitle()->prepend(__('Data Log'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    public function _isAllowed(){
        return $this->_authorization->isAllowed('Magenest_Popup::log');
    }
}