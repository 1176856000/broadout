<?php
namespace Magenest\Popup\Controller\Adminhtml\Template;

/**
 * Class Delete
 * @package Magenest\Popup\Controller\Adminhtml\Template
 */
class Delete extends \Magenest\Popup\Controller\Adminhtml\Template\Template {

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $params = $this->_request->getParams();
        try{
            /** @var \Magenest\Popup\Model\Template $popupTemplate */
            $popupTemplate = $this->_popupTemplateFactory->create();
            if(isset($params['id'])&&$params['id']){
                $popupTemplate->load($params['id']);
                if($this->getPopupsByTemplateId($params['id'])){
                    $message =  __('%1 is currently being used for a popup. You must remove the template from this configuration before deleting the template',$popupTemplate->getTemplateName());
                    throw new \Exception($message);
                }
                $popupTemplate->delete();
                $this->messageManager->addSuccess(__('The Popup template has been deleted.'));
            }
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception->getMessage());
        }
        $this->_redirect('*/*/index');
    }
}