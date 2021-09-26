<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class GoogleEcommerceTracking extends AbstractModel
{
    /**
     * @var \Magento\GoogleAnalytics\Helper\Data
     */
    protected $googleAnalyticsData;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager               $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                               $dataHelper
     * @param \Magento\Framework\Model\Context                                $context
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Customer\Model\Session                                 $customerSession
     * @param \Magento\Checkout\Model\Session                                 $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                         $request
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                           $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                               $orderFactory
     * @param \Magento\Directory\Model\RegionFactory                          $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress            $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                   $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeConfigInterface
     * @param \Magento\Framework\Url                                          $url
     * @param \Magento\GoogleAnalytics\Helper\Data                            $googleAnalyticsData
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null    $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null              $resourceCollection
     * @param array                                                           $data
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
        \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->googleAnalyticsData = $googleAnalyticsData;

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
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;

        if ($_section == 'head') {
            $order = $this->getLastOrder();
            if ($order && $order->getId() && $this->googleAnalyticsData->isGoogleAnalyticsAvailable()) {
                $html .= '<script type="text/javascript">
                            var _orderData = '. $this->getJsonData() .';
                            var _is_gaq_added = false;
                            var script_list = document.getElementsByTagName("script");
                            for(var i = 0; i < script_list.length; i++){
                                if (script_list[i].src.indexOf("google-analytics.com/ga.js") != -1){
                                    _is_gaq_added = true;
                                    break;
                                }
                            }

                            if (!_is_gaq_added){
                                var _gaq = _gaq || [];
                                _gaq.push(["_setAccount", "'. $this->getGoogleAnalyticsAccount() .'"]);
                                _gaq.push(["_trackPageview"]);
                                _gaq.push(["_addTrans",
                                    _orderData.order_id,    // transaction ID - required
                                    _orderData.store_name,  // affiliation or store name
                                    _orderData.total,       // total - required
                                    _orderData.tax,         // tax
                                    _orderData.shipping,    // shipping
                                    _orderData.city,        // city
                                    _orderData.state,       // state or province
                                    _orderData.country      // country
                                ]);

                                if (_orderData.items.length){
                                    for(var i = 0; i < _orderData.items.length; i++){
                                        var _orderItem = _orderData.items[i];
                                         _gaq.push(["_addItem",
                                            _orderData.order_id,    // transaction ID - required
                                            _orderItem.sku,         // SKU/code - required
                                            _orderItem.name,        // product name
                                            null,                   // category
                                            _orderItem.price,       // unit price - required
                                            _orderItem.qty          // quantity - required
                                          ]);
                                    }
                                }

                                _gaq.push(["_set", "currencyCode", "'. $this->getCurrencyCode($order) .'"]);
                                _gaq.push(["_trackTrans"]);         //submits transaction to the Analytics servers

                                (function(){
                                    var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
                                    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
                                    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
                                })();
                            }
                        </script>';
            }
        }

        return $html;
    }

    /**
     * Get google analytics account
     * @return string
     */
    public function getGoogleAnalyticsAccount()
    {
        return $this->_scopeConfigInterface->getValue(
            \Magento\GoogleAnalytics\Helper\Data::XML_PATH_ACCOUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getCode()
        );
    }

    /**
     * Get store name
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get json data
     * @return string
     */
    public function getJsonData()
    {
        $order      = $this->getLastOrder();
        $billing    = $order->getBillingAddress();

        $regionCode = $this->_regionFactory->create()->load($billing->getRegionId())->getCode();

        $data = [
            'order_id'          => $order->getIncrementId(),
            'store_name'        => $this->getStoreName(),
            'total'             => $order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(),
            'tax'               => $order->getTaxAmount(),
            'shipping'          => $order->getShippingAmount(),
            'city'              => $billing->getCity(),
            'state'             => $regionCode,
            'country'           => $billing->getCountryId(),
            'items'             => [],
        ];

        $childSku = [];
        foreach ($order->getAllItems() as $item) {
            if ($piID = $item->getParentItemId()) {
                $childSku[$piID] = $item->getSku();
            }
        }

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $this->_productFactory->create()->load($item->getProductId());

            if (isset($childSku[$item->getID()])) {
                $parentSku = $product->getSku();
                $variantSku = $item->getSku();
            } else {
                $parentSku = $variantSku = $product->getSku();
            }

            $item = [
                'sku'           => $variantSku,
                'name'          => $item->getName(),
                'price'         => $item->getPrice(),
                'qty'           => $item->getQtyOrdered(),
            ];
            $data['items'][] = $item;
        }

        return json_encode($data);
    }
}
