<?php
namespace Magenest\Popup\Controller\Popup;

use Magento\Framework\App\Action\Context;

/**
 * Class Preview
 * @package Magenest\Popup\Controller\Popup
 */
class Preview extends \Magento\Framework\App\Action\Action {
    /** @var  \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    protected $resultPageFactory;
    /** @var  \Magenest\Popup\Model\PopupFactory $popupFactory */
    protected $_popupFactory;

    /** @var  \Psr\Log\LoggerInterface $_logger */
    protected $_logger;

    /** @var  \Magento\Framework\Registry $_coreRegistry */
    protected $_coreRegistry;

    /**
     * Preview constructor.
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context
    ){
        $this->_popupFactory = $popupFactory;
        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $popupModel = $this->_popupFactory->create();
        try{
            $popup_id = $this->_request->getParam('popup_id');
            $htmlContent = $this->_request->getParam('html_content');
            $templateId = $this->_request->getParam('template_id');
            $backgroundImage = $this->_request->getParam('background_image');
            if ($htmlContent) {
                $this->_coreRegistry->register('html_content',$htmlContent);
            }
            if ($templateId) {
                $this->_coreRegistry->register('template_id',$templateId);
            }
            $this->_coreRegistry->register('background_image',$backgroundImage);
            if($popup_id){
                $popupModel->load($popup_id);
                if(!$popupModel->getPopupId()){
                    $this->messageManager->addError(__('This Popup doesn\'t exist'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/index');
                }
            }
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception->getMessage());
        }
        $this->_coreRegistry->register('popup',$popupModel);
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Preview Popup'));
        return $resultPage;
    }
}