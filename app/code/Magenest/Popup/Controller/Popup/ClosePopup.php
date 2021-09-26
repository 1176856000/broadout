<?php
namespace Magenest\Popup\Controller\Popup;

/**
 * Class ClosePopup
 * @package Magenest\Popup\Controller\Popup
 */
class ClosePopup extends \Magenest\Popup\Controller\Popup\Popup {

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * ClosePopup constructor.
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Magenest\Popup\Helper\Cookie $cookieHelper
     * @param \Magenest\Popup\Helper\Data $dataHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Helper\Cookie $cookieHelper,
        \Magenest\Popup\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        parent::__construct($popupFactory, $cookieHelper, $dataHelper, $dateTime, $resultPageFactory, $context);
        $this->_json = $json;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $response = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        if(isset($params['popup_id'])&&$params['popup_id']&&$params['flag'] == 0){
            /** @var \Magenest\Popup\Model\Popup $popupModel */
            $popupModel = $this->_popupFactory->create()->load($params['popup_id']);
            $popup_click = (int)$popupModel->getClick()+1;
            $popup_view = (int)$popupModel->getView();
            $ctr = (float)($popup_click/$popup_view)*100;
            $popupModel->setClick($popup_click);
            $popupModel->setView($popup_view);
            $popupModel->setCtr($ctr);
            $popupModel->save();
        }
        $data = $this->_json->serialize([]);
        $response->setContents($data);
        return $response;
    }
}