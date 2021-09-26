<?php
namespace Magenest\Popup\Block\Popup;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Display
 * @package Magenest\Popup\Block\Popup
 */
class Display extends \Magento\Framework\View\Element\Template{

    /** @var \Magenest\Popup\Helper\Data $_helperData */
    protected $_helperData;

    /** @var  \Magenest\Popup\Model\PopupFactory $_popupFactory */
    protected $_popupFactory;

    /** @var  \Magenest\Popup\Model\TemplateFactory $_templateFactory */
    protected $_templateFactory;
    /** @var \Magento\Cms\Model\Template\FilterProvider $_filterProvider */
    protected $_filterProvider;

    /** @var \Magento\Framework\Stdlib\CookieManagerInterface $_cookieManager */
    protected $_cookieManager;

    /** @var  \Magenest\Popup\Helper\Cookie $_helperCookie */
    protected $_helperCookie;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime $_dateTime */
    protected $_dateTime;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $_resourceConnection;

    /** @var StoreManagerInterface  */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * Display constructor.
     * @param \Magenest\Popup\Helper\Data $helperData
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Magenest\Popup\Model\TemplateFactory $templateFactory
     * @param \Magenest\Popup\Helper\Cookie $helperCookie
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param array $data
     */
    public function __construct(
        \Magenest\Popup\Helper\Data $helperData,
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Model\TemplateFactory $templateFactory,
        \Magenest\Popup\Helper\Cookie $helperCookie,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json,
        array $data = []
    ){
        $this->_helperData = $helperData;
        $this->_popupFactory = $popupFactory;
        $this->_templateFactory = $templateFactory;
        $this->_filterProvider = $filterProvider;
        $this->_cookieManager = $cookieManager;
        $this->_helperCookie = $helperCookie;
        $this->_dateTime = $dateTime;
        $this->_resourceConnection = $resourceConnection;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_json = $json;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkCustomerGroup(){
        $customerGroupIds = explode(',',$this->getPopup()->getCustomerGroupIds());
        $customerGroup = $this->_customerSession->getCustomerGroupId();
        return in_array($customerGroup,$customerGroupIds) || in_array(\Magento\Customer\Api\Data\GroupInterface::CUST_GROUP_ALL,$customerGroupIds);
    }

    /**
     * @return bool
     */
    public function checkPageToShow()
    {
        if ($this->_helperData->isModuleEnable()) {
            return true;
        }
        return false;
    }

    /**
     * @return false|string
     */
    public function getDataDisplay()
    {
        /** @var \Magenest\Popup\Model\Popup $popup */
        $popup = $this->getPopup();
        $data = $popup->getData();
        $class = $this->getTemplateClassDefault($popup->getPopupTemplateId());
        $data['class'] = $class;
        $urlCheckCookie = $this->getUrlCheckCookie();
        $data['url_check_cookie'] = $urlCheckCookie;
        $urlClosePopup = $this->getUrlClosePopup();
        $data['url_close_popup'] = $urlClosePopup;
        $lifetime = $this->getCookieLifeTime();
        $data['lifetime'] = $lifetime;
        if (isset($data['background_image'])) {
            $imageArr= (array) $this->_json->unserialize($data['background_image']);
            $background_image = (array) reset($imageArr);
            $styleExtend = '.magenest-popup-inner{background-image: url('.$background_image['url'].') !important;}';
            $data['css_style'] .= $styleExtend;
        }
        return json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * @return \Magenest\Popup\Model\Popup|string
     * @throws \Exception
     */
    public function getPopup()
    {
        $popupIdCookies = $this->getCookie() == null ? [] : $this->getCookie();
        $today = $this->_dateTime->date('Y-m-d');
        $timestamp_today = $this->_dateTime->timestamp($today);
        $popupIdArray = $this->getPopupIdArray();
        $data = [];
        if (!empty($popupIdArray)) {
            $popupCollections = $this->_popupFactory->create()
                ->getCollection()
                ->addFieldToFilter('popup_id', ['in', [$popupIdArray]]);
            if(!empty($popupCollections)) {
                foreach ($popupCollections as $popupCollection) {
                    $start_date = $popupCollection->getStartDate();
                    $end_date = $popupCollection->getEndDate();
                    if ($start_date == '' && $end_date == '') {
                        $data[] = $popupCollection;
                    } elseif ($start_date == '' && $end_date != '') {
                        $end_date_timestamp = $this->_dateTime->timestamp($end_date);
                        if ($end_date_timestamp >= $timestamp_today) {
                            $data[] = $popupCollection;
                        }
                    } elseif ($start_date != '' && $end_date == '') {
                        $start_date_timestamp = $this->_dateTime->timestamp($start_date);
                        if ($start_date_timestamp <= $timestamp_today) {
                            $data[] = $popupCollection;
                        }
                    } elseif ($start_date != '' && $end_date != '') {
                        $start_date_timestamp = $this->_dateTime->timestamp($start_date);
                        $end_date_timestamp = $this->_dateTime->timestamp($end_date);
                        if ($start_date_timestamp <= $timestamp_today && $end_date_timestamp >= $timestamp_today) {
                            $data[] = $popupCollection;
                        }
                    }
                }
            }
        }
        $popupModel = '';
        if(!empty($data)){
            $min = null;
            /** @var \Magenest\Popup\Model\Popup $popup */
            foreach ($data as $popup){
                $priority = $popup->getPriority();
                foreach ($popupIdCookies as $popupIdCookie){
                    if(($popupIdCookie['popup_id'] == $popup->getPopupId()) && ($popup->getEnableCookieLifetime() == 1)){
                        $life_time = $popup->getCookieLifetime()*1000;
                        if($timestamp_today - $popupIdCookie['timestamp'] <= $life_time) continue;
                    }
                }
                $min = $min==null?$priority:$min;
                $popupModel = $min>=$priority?$popup:$popupModel;
            }
        }

        if($popupModel instanceof \Magenest\Popup\Model\Popup ){
            $html_content = $popupModel->getHtmlContent();
            if(isset($html_content) && is_string($html_content)) {
                $content = $this->_filterProvider->getBlockFilter()->filter($html_content);
                $content .= '<span id="copyright"></span>';
                $content = "<div class='magenest-popup-inner'>".$content."</div>";
            } else {
                $content = "";
            }
            $popupModel->setHtmlContent($content);
        }else{
            $popupModel = $this->_popupFactory->create();
        }
        return $popupModel;
    }

    /**
     * @return mixed
     */
    public function getCurrentFullActionName(){
        return $this->getRequest()->getFullActionName();
    }

    /**
     * @return bool
     */
    public function isPreview(){
        $fullActionName = $this->getCurrentFullActionName();
        if((($fullActionName == "magenest_popup_popup_preview" || $fullActionName == "magenest_popup_template_preview")&&
            ($this->getRequest()->getParam('popup_id') != '' || $this->getRequest()->getParam('id') != '') ||
            $this->getRequest()->getParam('html_content'))
        ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $templateId
     * @return array|mixed|string|null
     */
    public function getTemplateClassDefault($templateId){
        /** @var \Magenest\Popup\Model\Template $templateModel */
        $templateModel = $this->_templateFactory->create()->load($templateId);
        if($templateModel->getTemplateId()){
            return $templateModel->getData('class');
        }else{
            return 'popup-default-1';
        }
    }

    /**
     * @return string
     */
    public function getUrlCheckCookie(){
        return $this->getUrl('magenest_popup/popup/checkCookie');
    }

    /**
     * @return string
     */
    public function getUrlClosePopup(){
        return $this->getUrl('magenest_popup/popup/closePopup');
    }

    /**
     * @return array|null
     */
    public function getCookie(){
        $cookies = $this->_helperCookie->get();
        if($cookies){
            $cookieArr = $this->_json->unserialize($cookies ?? 'null');
            $popupIds = [];
            $i = 0;
            foreach ($cookieArr as $key => $value){
                if($key == 'view_page'){
                    $i++;
                    continue;
                }
                $popupIds[] = [
                    'popup_id' => $key,
                    'timestamp' => $value
                ];
            }
            return $popupIds;
        }else{
            return null;
        }
    }

    /**
     * @return int
     */
    public function getCookieLifeTime(){
        /** @var \Magenest\Popup\Model\Popup $collection */
        $collection = $this->_popupFactory->create()
            ->getCollection()
            ->addFieldToFilter('enable_cookie_lifetime',1)
            ->setOrder('cookie_lifetime','DESC')
            ->getFirstItem();
        return $collection->getCookieLifetime() != null ? $collection->getCookieLifetime() : 86400;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPopupIdArray()
    {
        $layout = $this->getLayout()->getUpdate()->getHandles();
        $connection = $this->_resourceConnection->getConnection();
        $layoutUpdateTable = $this->_resourceConnection->getTableName('layout_update');
        $select = $connection->select()->from($layoutUpdateTable,'layout_update_id')
            ->where('handle IN (?)', $layout);
        $layout_update_id = $connection->fetchCol($select);
        $popupLayoutTable = $this->_resourceConnection->getTableName('magenest_popup_layout');
        $select = $connection->select()->from($popupLayoutTable, 'popup_id')
            ->where('layout_update_id IN (?)', $layout_update_id);
        $popup_id_array =  $connection->fetchCol($select);
        return $popup_id_array;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHomeUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getContactUrl()
    {
        return $this->_storeManager->getStore()->getUrl('contact');
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param $popup
     * @return array|false|string[]
     */
    public function getVisibleStore($popup)
    {
        if (!empty($popup)) {
            $visibleStore = $popup->getVisibleStores();
            $storeIds = str_replace(',', ' ', $visibleStore);
            return explode(' ', $storeIds);
        } else {
            return [];
        }
    }

    /**
     * @param $popup
     * @return bool
     */
    public function checkStorePopup($popup)
    {
        $storeId = $this->getStoreId();
        $visibleStore = $this->getVisibleStore($popup);
        $result = in_array("0", $visibleStore) || in_array($storeId, $visibleStore) ? true : false;
        return $result;
    }

    /**
     * @return bool
     */
    public function enableSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
