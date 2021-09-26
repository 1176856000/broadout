<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

use Magento\Framework\Encryption\Encryptor;

class Tradedoubler extends AbstractModel
{
    const TYPE_ID = 12;
    /**
     * Storage Name
     * @var string
     */
    const STORAGE_NAME = 'TRADEDOUBLER';

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $helperOutput;
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listProduct;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data $dataHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Framework\Url $url
     * @param \Magento\Catalog\Helper\Output $helperOutput
     * @param \Magento\Catalog\Block\Product\ListProduct $listProduct
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Url $url,
        \Magento\Catalog\Helper\Output $helperOutput,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperOutput = $helperOutput;
        $this->listProduct  = $listProduct;
        $this->encryptor = $encryptor;

        parent::__construct(
            $cookieManager,
            $dataHelper,
            $context,
            $registry,
            $customerSession,
            $checkoutSession,
            $request,
            $storeManager,
            $productFactory,
            $categoryFactory,
            $orderFactory,
            $regionFactory,
            $remoteAddress,
            $imageHelper,
            $scopeConfigInterface,
            $url,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Get organization id
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->getAdditionalDataValue('organization_id');
    }

    /**
     * Cps enable
     * @return bool
     */
    public function getCpsEnable()
    {
        return $this->getAdditionalDataValue('cps_enable');
    }

    /**
     * Get sale event id
     * @return int
     */
    public function getSaleEventId()
    {
        return $this->getAdditionalDataValue('sale_event_id');
    }

    /**
     * Is cpl enable
     * @return bool
     */
    public function getCplEnable()
    {
        return $this->getAdditionalDataValue('cpl_enable');
    }

    /**
     * Get lead event id
     * @return int
     */
    public function getLeadEventId()
    {
        return $this->getAdditionalDataValue('lead_event_id');
    }

    /**
     * Get checksum code
     * @return string
     */
    public function getChecksumCode()
    {
        return $this->getAdditionalDataValue('checksum_code');
    }

    /**
     * Id retargeting enable
     * @return boolean
     */
    public function getRetargetingEnable()
    {
        return $this->getAdditionalDataValue('retargeting_enable');
    }

    /**
     * Get retargetion tag id
     * @param string  $tag
     * @return string
     */
    public function getRetargetingTagId($tag)
    {
        $additionalData = $this->getAdditionalDataArray();
        return isset($additionalData["retargeting_{$tag}_tagid"])
            ? (int)$additionalData["retargeting_{$tag}_tagid"]
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;
        $scheme = $this->_request->getScheme();

        if ($_section == parent::SECTION_BODYBEGIN) {
            // Sale (checkout_success).

            if (!$this->getCpsEnable()) {
                return;
            }

            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

                if ($this->getCpsEnable() == 1) {

                    // Confirmation Page.
                    $products = [];
                    foreach ($order->getAllVisibleItems() as $item) {
                        $productParams = [
                            'f1' => $item->getSku(),
                            'f2' => $item->getName(),
                            'f3' => round($item->getPrice(), 2),
                            'f4' => round($item->getQtyOrdered()),
                        ];
                        $products[] = urldecode(http_build_query($productParams));
                    }

                    $params = [
                        'organization'  => $this->getOrganizationId(),
                        'event'         => $this->getSaleEventId(),
                        'tduid'         => $this->_getTduid(),
                        'type'          => 'iframe',

                        'orderNumber'   => $order->getIncrementId(),
                        'orderValue'    => $totalAmount,
                        'currency'      => $this->getCurrencyCode($order),
                        'reportInfo'    => implode('|', $products),
                    ];

                    if ($checksumCode = $this->getChecksumCode()) {
                        $hash = $this->encryptor->hash($checksumCode . $order->getIncrementId() . $totalAmount, Encryptor::HASH_VERSION_MD5);
                        $params['checksum'] = 'v04'. $hash;
                    }

                    if ($this->getSaleEventId()) {
                        $html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                        <iframe src="'. $scheme .'://tbs.tradedoubler.com/report?'. http_build_query($params) .'" frameborder="0" width="1" height="1"></iframe>
                                    </div>';
                    }

                } elseif ($this->getCpsEnable() == 2) {
                    // Product Level Tracking.
                    $productsStr = '';
                    foreach ($order->getAllVisibleItems() as $item) {

                        $_product = $item->getProduct();
                        if (!$_product) {
                            $_product = $this->_productFactory->create()->load($item->getProductId());
                            $item->setProduct($_product);
                        }

                        $productParams = [
                            'gr'    => $_product->getData('affiliate_tradedoubler_groupid'),
                            'i'     => substr(preg_replace("/[^a-zA-Z0-9._-]/", "", $item->getSku()), 0, 20),
                            'n'     => substr($item->getName(), 0, 20),
                            'v'     => round($item->getPrice(), 2),
                            'q'     => round($item->getQtyOrdered()),
                        ];

                        $productStr = '';
                        foreach ($productParams as $key => $value) {
                            $productStr .= $key .'('. rawurlencode($value) .')';
                        }
                        $productsStr .= 'pr('. $productStr .')';
                    }

                    $params = [
                        'o'         => $this->getOrganizationId(),
                        'event'     => '51',
                        'ordnum'    => $order->getIncrementId(),
                        'curr'      => $this->getCurrencyCode($order),
                        'tduid'     => $this->_getTduid(),
                        'type'      => 'iframe',
                        'enc'       => '3',
                        'basket'    => $productsStr,
                    ];

                    if ($checksumCode = $this->getChecksumCode()) {
                        $hash = $this->encryptor->hash($checksumCode . $order->getIncrementId() . $totalAmount, Encryptor::HASH_VERSION_MD5);
                        $params['chksum'] = 'v04'. $hash;
                    }

                    $paramsStr = '';
                    foreach ($params as $key => $value) {
                        if ($key != 'basket') {
                            $value = urlencode($value);
                        }
                        $paramsStr .= $key .'('. $value .')';
                    }

                    $html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                    <iframe src="'. $scheme .'://tbs.tradedoubler.com/report?'. $paramsStr .'" frameborder="0" width="1" height="1"></iframe>
                                </div>';
                }
            }

        } elseif ($_section == parent::SECTION_BODYEND) {
            // Lead (registration_success_pages).
            if ($this->getCplEnable() && $this->getLeadEventId() && isset($_includeon['registration_success_pages'])) {
                $currentCustommer = $this->_customerSession->getCustomer()->getId();

                $params = [
                    'organization'  => $this->getOrganizationId(),
                    'event'         => $this->getLeadEventId(),
                    'tduid'         => $this->_getTduid(),
                    'type'          => 'iframe',

                    'leadNumber'    => $currentCustommer
                ];

                $orderValueForLead = '1';

                if ($checksumCode = $this->getChecksumCode()) {
                    $hash = $this->encryptor->hash($checksumCode . $params['leadNumber'] . $orderValueForLead, Encryptor::HASH_VERSION_MD5);
                    $params['checksum'] = 'v04'. $hash;
                }

                $paramsStr = '';
                foreach ($params as $key => $value) {
                    $paramsStr .= $key .'('. urlencode($value) .')';
                }

                $html .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                <iframe src="'. $scheme .'://tbl.tradedoubler.com/report?'. $paramsStr .'" frameborder="0" width="1" height="1"></iframe>
                            </div>';
            }
        }

        /* Retargeting */
        if ($this->getRetargetingEnable() && $_section == parent::SECTION_BODYEND) {

            $template = <<<JS
<script type="text/javascript">

    \$async = true; // true : Asynchronous script / false : Synchronous Script
    /*_COOKIE_*/

    var TDConf = TDConf || {};
    /*_TDCONF_*/

    if(typeof (TDConf) != "undefined") {
        /*_SET_TDUID_*/
        TDConf.sudomain = ("https:" == document.location.protocol) ? "swrap" : "wrap";
        TDConf.host = ".tradedoubler.com/wrap";
        TDConf.containerTagURL = (("https:" == document.location.protocol) ? "https://" : "http://")  + TDConf.sudomain + TDConf.host;

        if (typeof (TDConf.Config) != "undefined") {
            if (\$async){

                var TDAsync = document.createElement('script');
                    TDAsync.src = TDConf.containerTagURL  + "?id="+ TDConf.Config.containerTagId;
                    TDAsync.async = "yes";
                    TDAsync.width = 0;
                    TDAsync.height = 0;
                TDAsync.frameBorder = 0;
                    document.body.appendChild(TDAsync);
            }
            else{
                document.write(unescape("%3Cscript src='" + TDConf.containerTagURL  + "?id="+ TDConf.Config.containerTagId +" ' type='text/javascript'%3E%3C/script%3E"));
            }
        }
    }
</script>
JS;

            $templateSetCookie = <<<JS
    function getVar(name) {
        get_string = document.location.search;
        return_value = '';
        do {
            name_index = get_string.indexOf(name + '=');
            if(name_index != -1) {
                get_string = get_string.substr(name_index + name.length + 1,
                get_string.length - name_index);
                end_of_value = get_string.indexOf('&');
                if(end_of_value != -1) {
                    value = get_string.substr(0, end_of_value);
                } else {
                    value = get_string;
                }
                if(return_value == '' || value == '') {
                    return_value += value;
                } else {
                    return_value += ', ' + value;
                }
            }
        }
        while(name_index != -1) {
            space = return_value.indexOf('+');
        }
        while(space != -1) {
            return_value = return_value.substr(0, space) + ' ' +
            return_value.substr(space + 1, return_value.length);
            space = return_value.indexOf('+');
        }
        return(return_value);
    }

    function setCookie(name, value, expires, path, domain, secure) {
        var today = new Date();
        today.setTime( today.getTime() );
        if ( expires ) {
            expires = expires * 1000 * 60 * 60 * 24;
        }
        var expires_date = new Date( today.getTime() + (expires) );
        document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires_date.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
    }

    var mytduid = getVar('tduid');
    if  (mytduid!='') {
        setCookie('TRADEDOUBLER', mytduid, 365);
    }
JS;

            $templateGetCookie = <<<JS
    function getCookie(name) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        } else {
            begin += 2;
        }
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
        return unescape(dc.substring(begin + prefix.length, end));
    }
JS;

            $templateTduid = <<<JS
        TDConf.Config.tduid = getCookie("TRADEDOUBLER");
JS;

            switch (true) {

                // Check-out Page.
                case $retargetingTagId = $this->getRetargetingTagId('checkout') && isset($_includeon['checkout_success'])? $this->getRetargetingTagId('checkout') : 0:

                    $order = $this->getLastOrder();
                    if ($order && $order->getId()) {

                        $totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

                        // Confirmation Page.
                        $products = [];
                        foreach ($order->getAllVisibleItems() as $item) {

                            $_product = $item->getProduct();
                            if (!$_product) {
                                $_product = $this->_productFactory->create()->load($item->getProductId());
                                $item->setProduct($_product);
                            }

                            $products[] = [
                                'id'        => $item->getSku(),
                                'price'     => round($item->getPrice(), 2),
                                'currency'  => $this->getCurrencyCode($order),
                                'name'      => $item->getName(),
                                'grpId'     => $_product->getData('affiliate_tradedoubler_groupid') ?: '',
                                'qty'       => round($item->getQtyOrdered()),
                            ];
                        }

                        $params = [
                            'products'      => $products,
                            'orderId'       => $order->getIncrementId(),
                            'orderValue'    => $totalAmount,
                            'currency'      => $this->getCurrencyCode($order),
                            'containerTagId'=> $retargetingTagId,
                        ];

                        $cookie = $templateGetCookie;
                        $config = 'TDConf.Config = '. json_encode($params) .';';
                        $tduid = $templateTduid;
                    }
                    break;

                // Registration.
                case $retargetingTagId = $this->getRetargetingTagId('registration') && isset($_includeon['registration_success_pages'])? $this->getRetargetingTagId('registration') : 0:
                    $config = 'TDConf.Config = {
                        protocol : document.location.protocol,
                        containerTagId : "'. $retargetingTagId .'"
                    };';
                    break;

                // Basket Page.
                case $retargetingTagId = $this->getRetargetingTagId('basket') && isset($_includeon['cart_page'])? $this->getRetargetingTagId('basket') : 0:
                    $quote = $this->_checkoutSession->getQuote();
                    $products = [];
                    foreach ($quote->getAllVisibleItems() as $item) {
                        $products[] = [
                            'id'        => $item->getSku(),
                            'price'     => round($item->getPrice(), 2),
                            'currency'  => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                            'name'      => $item->getName(),
                            'qty'       => round($item->getQty()),
                        ];
                    }

                    $config = 'TDConf.Config = {
                        products: '. json_encode($products) .',
                        containerTagId : "'. $retargetingTagId .'"
                    };';
                    break;

                // Product Pages.
                case $retargetingTagId = $this->getRetargetingTagId('product') && isset($_includeon['product_page'])? $this->getRetargetingTagId('product') : 0:
                    if ($product = $this->_registry->registry('current_product')) {
                        // Get the first category assigned to the item.  This is retailer specific.
                        $categoryIds = $product->getCategoryIds();

                        $firstCategoryName = '';
                        if (count($categoryIds)) {
                            $firstCategoryName = $this->_categoryFactory->create()->load($categoryIds[0])->getName();
                        }

                        $params = [
                            'productId'         => $product->getSku(),
                            'category'          => $firstCategoryName,
                            'brand'             => '',
                            'productName'       => $product->getName(),
                            'productDescription'=> $this->helperOutput->productAttribute($product, $product->getShortDescription(), 'short_description'),
                            'price'             => round($product->getPrice(), 2),
                            'currency'          => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                            'url'               => $product->getProductUrl(),
                            'imageUrl'          => $product->getImageUrl(),
                            'containerTagId'    => $retargetingTagId,
                        ];

                        $config = 'TDConf.Config = '. json_encode($params) .';';
                    }
                    break;

                // Product Listings.
                case $retargetingTagId = $this->getRetargetingTagId('category') && isset($_includeon['category_page'])? $this->getRetargetingTagId('category') : 0:
                    if ($this->listProduct) {
                        $products = [];
                        foreach ($this->listProduct->getLoadedProductCollection() as $item) {
                            $products[] = [
                                'id'        => $item->getSku(),
                                'price'     => round($item->getPrice(), 2),
                                'currency'  => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                                'name'      => $item->getName(),
                            ];
                        }

                        $firstCategoryName = '';

                        if (!empty($item) && $item->getId()) {
                            // Get the first category assigned to the item.  This is retailer specific.
                            $categoryIds = $item->getCategoryIds();

                            if (count($categoryIds)) {
                                $firstCategoryName = $this->_categoryFactory->create()->load($categoryIds[0])->getName();
                            }
                        }

                        $params = [
                            'products'      => $products,
                            'Category_name' => $firstCategoryName,
                            'containerTagId'=> $retargetingTagId,
                        ];

                        $config = 'TDConf.Config = '. json_encode($params) .';';
                    }
                    break;

                // Home page.
                case $retargetingTagId = $this->getRetargetingTagId('homepage') && isset($_includeon['home_page'])? $this->getRetargetingTagId('homepage') : 0:
                    $config = 'TDConf.Config = {
                        protocol : document.location.protocol,
                        containerTagId : "'. $retargetingTagId .'"
                    };';
                    break;
            }

            if (!empty($config)) {
                $html .= str_replace(
                    [
                        '/*_COOKIE_*/',
                        '/*_TDCONF_*/',
                        '/*_SET_TDUID_*/'
                    ],
                    [
                        (isset($cookie)? $cookie : $templateSetCookie),
                        $config,
                        (isset($tduid)? $tduid : '')
                    ],
                    $template
                );
            }

        }

        return $html;
    }

    /**
     * Get Tradoubled User Id
     * @return string
     */
    protected function _getTduid()
    {
        return $this->_cookieManager->getCookie(self::STORAGE_NAME);
    }
}
