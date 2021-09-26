<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Chango extends AbstractModel
{
    const JAVASCRIPT_IMPLEMENTING_METHOD = 'javascript';
    const IMAGE_IMPLEMENTING_METHOD = 'image';

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager            $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                            $dataHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Customer\Model\Session                              $customerSession
     * @param \Magento\Checkout\Model\Session                              $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                      $request
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                        $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                       $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                            $orderFactory
     * @param \Magento\Directory\Model\RegionFactory                       $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress         $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfigInterface
     * @param \Magento\Framework\Url                                       $url
     * @param \Magento\Checkout\Model\Cart                                 $cart
     * @param \Magento\Framework\Escaper                                   $escaper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
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
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->cart    = $cart;
        $this->escaper = $escaper;

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
     * Get chango id
     * @return int
     */
    public function getChangoId()
    {
        return $this->getAdditionalDataValue('chango_id');
    }

    /**
     * Get conversion id
     * @return int
     */
    public function getConversionId()
    {
        return $this->getAdditionalDataValue('conversion_id');
    }

    /**
     * Get implementing method
     * @return int
     */
    public function getImplementingMethod()
    {
        return $this->getAdditionalDataValue('implementing_method');
    }

    /**
     * Get core template
     * @param  string $type
     * @return string
     */
    public function getCodeTemplate($type)
    {
        switch ($type) {
            case 'conversion_javascript':
                return '<script type="text/javascript">//<![CDATA[
   var __chconv__ = {"order_id":"[ORDER_ID]","cost":"[COST]","conversion_id":"[CONVERSION_ID]","quantity":"[QUANTITY]","u1":"[CUSTOMER_ID]","u2":"[SKU_LIST]","u4":"[PAYMENT_TYPE]","u5":"[CONVERSION_TYPE]"};
   (function() {
      if (typeof(__chconv__) == "undefined") return;
      var e = encodeURIComponent; var p = [];
      for(var i in __chconv__){p.push(e(i) + "=" + e(__chconv__[i]))}
      (new Image()).src = document.location.protocol + "//as.chango.com/conv/i;" + (new Date()).getTime() + "?" + p.join("&");
   })();
//]]></script>';
            case 'conversion_image':
                return '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
   <img src="https://as.chango.com/conv/i?conversion_id=[CONVERSION_ID]&order_id=[ORDER_ID]&cost=[COST]&quantity=[QUANTITY]&u1=[CUSTOMER_ID]&u2=[SKU_LIST]&u4=[PAYMENT_TYPE]&u5=[CONVERSION_TYPE]" width="1" height="1" />
  </div>';
            case 'optimization_javascript':
                return '<script type="text/javascript">//<![CDATA[
   var __cho__ = {"data":{"sku":"[SKU_VALUE]","pt":"[PT_VALUE]","keyword":"[KEYWORD_VALUE]","ss":"[SS_VALUE]","na":"[PRODUCT_NAME]","sp":"[SALE_PRICE]","pc":"[PRODUCT_CATEGORY]","puid2":"[PUID]","crt":"[CRT_VALUE]","op":"[ORIGINAL_PRICE]","p":"[PAGE_URL]","r":"[REFERRING_URL]"},"pid":"[PID]"};

   (function() {
      var c = document.createElement("script");
      c.type = "text/javascript";
      c.async = true;
      c.src = document.location.protocol + "//cc.chango.com/static/o.js";
      var s = document.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(c, s);
     })();
  //]]></script>';
            case 'optimization_image':
                return '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
   <img src="https://cc.chango.com/conv/c/o?pid=[PID]&puid2=[PUID]&sku=[SKU_VALUE]&keyword=[KEYWORD_VALUE]&p=[PAGE_URL]&r=[REFERRING_URL]&__na=[PRODUCT_NAME]&__pt=[PT_VALUE]&__op=[ORIGINAL_PRICE]&__sp=[SALE_PRICE]&__pc=[PRODUCT_CATEGORY]" width="1" height="1" />
</div>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $code = '';
        switch ($_section) {
            case parent::SECTION_BODYBEGIN:
                $order = $this->getLastOrder();
                if ($order && $order->getId()) {
                    $qty = 0;
                    $skuList = [];
                    foreach ($order->getAllVisibleItems() as $item) {
                        $qty += $item->getQtyOrdered();
                        $skuList[] = $item->getSku();
                    }

                    $paymentMethod = '';
                    if ($payment = $order->getPayment()) {
                        if ($paymentMethodInstance = $payment->getMethodInstance()) {
                            $paymentMethod = $paymentMethodInstance->getTitle();
                        }
                    }

                    $params = [
                        'COST'              => $order->getSubtotal(),
                        'ORDER_ID'          => $order->getIncrementId(),
                        'CONVERSION_ID'     => $this->getConversionId(),
                        'CONVERSION_TYPE'   => 'purchase',
                        'QUANTITY'          => $qty,
                        'SKU_LIST'          => implode(',', $skuList),
                        'CUSTOMER_ID'       => $this->_customerSession->getCustomer()->getId(),
                        'PAYMENT_TYPE'      => $paymentMethod,
                    ];

                    $code = $this->getCodeTemplate('conversion_'.$this->getImplementingMethod());
                }
                break;
            case parent::SECTION_BODYEND: //optimization pixel
                $product = $this->_registry->registry('current_product');
                $category = $this->_registry->registry('current_category');

                $isCart = $this->_request->getControllerName() == 'cart' && $this->_request->getActionName() == 'index';

                $fPrice = 0;
                $oPrice = 0;
                if ($product) {
                    $fPrice = $product->getPriceModel()->getFinalPrice(1, $product);
                    $oPrice = $product->getPriceModel()->getBasePrice($product, 1);
                }

                if ($isCart) {
                    $cartData = [];
                    foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
                        $_iProduct = $item->getProduct();
                        if ($_iProduct && $_iProduct->getId()) {
                            $cartData[] = [
                                'na' => $_iProduct->getName(),
                                'sku' => $item->getSku(),
                            ];
                        }
                    }
                }

                $params = [
                    'PID'       => $this->getChangoId(),
                    'PUID'      => $this->_customerSession->getCustomer()->getId(),
                    'PAGE_URL'  => $this->_url->getCurrentUrl(),
                    'REFERRING_URL' => $this->_request->getServer('HTTP_REFERER'),
                    'PRODUCT_NAME' => $product ? $product->getName() : '',
                    'SKU_VALUE' => $product ? $product->getSku() : '',
                    'KEYWORD_VALUE' => $product ? $product->getName() : '',
                    'PT_VALUE' => $product ? 'product' : ($category ? 'category' : ''),
                    'SS_VALUE' => $product ? $product->getName() : '',
                    'ORIGINAL_PRICE' => $oPrice,
                    'SALE_PRICE' => $fPrice,
                    'PRODUCT_CATEGORY' => $category ? $category->getName() : '',
                    'CRT_VALUE' => !empty($cartData) ? json_encode($cartData) : '',
                ];

                $code = $this->getCodeTemplate('optimization_'.$this->getImplementingMethod());

                if (!$isCart) {
                    $code = str_replace(',"crt":"[CRT_VALUE]"', '', $code);
                }

                break;
            default:
                $code = '';
        }

        if ($code) {
            $urlEC = ($this->getImplementingMethod() == self::IMAGE_IMPLEMENTING_METHOD);
            foreach ($params as $key => $value) {
                if ($key != 'CRT_VALUE') {
                    if ($urlEC) {
                        $value = urlencode($value);
                    } else {
                        $value = $this->escaper->escapeJsQuote($value, "\"");
                    }
                } else {
                    if (!$urlEC) {
                        $value = $this->escaper->escapeJsQuote($value, "\"");
                    }
                }
                $code = str_replace('['.$key.']', $value, $code);
            }
        }

        return $code;
    }
}
