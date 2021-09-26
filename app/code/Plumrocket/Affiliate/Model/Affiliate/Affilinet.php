<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

use Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter as TrackingParameter;
use Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute as ProductAttribute;

class Affilinet extends AbstractModel
{
    const ALL_PAGES = 'all';
    const CHECKOUT_SUCCESS_PAGE = 'checkout_success';
    const HOME_PAGE = 'home_page';
    const PRODUCT_PAGE = 'product_page';
    const CATEGORY_PAGE = 'category_page';
    const CART_PAGE = 'cart_page';
    const SEARCH_RESULT_PAGE = 'catalogsearch_result_page';

    const MODULE_ORDER_TRACKING = 'OrderTracking';
    const MODULE_PROFILING = 'Profiling';

    public $data;

    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Domain
     */
    protected $domain;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Event
     */
    protected $trackingEvent;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter
     */
    protected $trackingParameter;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute
     */
    protected $productAttribute;
    /**
     * @var \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling\Event
     */
    protected $profilingEvent;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollection;
    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $catalogSearchHelper;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layoutInterface;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager                                 $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                                                 $dataHelper
     * @param \Magento\Framework\Model\Context                                                  $context
     * @param \Magento\Framework\Registry                                                       $registry
     * @param \Magento\Customer\Model\Session                                                   $customerSession
     * @param \Magento\Checkout\Model\Session                                                   $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                                           $request
     * @param \Magento\Store\Model\StoreManagerInterface                                        $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                                             $productFactory
     * @param \Magento\Sales\Model\OrderFactory                                                 $orderFactory
     * @param \Magento\Directory\Model\RegionFactory                                            $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress                              $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                                     $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                                $scopeConfigInterface
     * @param \Magento\Framework\Url                                                            $url
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Domain                        $domain
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Event                $trackingEvent
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter            $trackingParameter
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute    $productAttribute
     * @param \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling\Event               $profilingEvent
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory                   $categoryCollection
     * @param \Magento\CatalogSearch\Helper\Data                                                $catalogSearchHelper
     * @param \Magento\Framework\View\LayoutInterface                                           $layoutInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null                      $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                                $resourceCollection
     * @param array                                                                             $data
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
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Domain                      $domain,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Event              $trackingEvent,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Parameter          $trackingParameter,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Tracking\Product\Attribute  $productAttribute,
        \Plumrocket\Affiliate\Model\Config\Source\Affilinet\Profiling\Event             $profilingEvent,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory                 $categoryCollection,
        \Magento\CatalogSearch\Helper\Data                                              $catalogSearchHelper,
        \Magento\Framework\View\LayoutInterface                                         $layoutInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->domain               = $domain;
        $this->trackingEvent        = $trackingEvent;
        $this->trackingParameter    = $trackingParameter;
        $this->productAttribute     = $productAttribute;
        $this->profilingEvent       = $profilingEvent;
        $this->categoryCollection   = $categoryCollection;
        $this->catalogSearchHelper  = $catalogSearchHelper;
        $this->layoutInterface      = $layoutInterface;

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
     * Retrieve program id
     * @return string
     */
    public function getProgramId()
    {
        return $this->getAdditionalDataValue('program_id');
    }

    /**
     * Retrieve program id
     * @return string
     */
    public function getTagId()
    {
        return $this->getAdditionalDataValue('tag_id', 'TAG-ID-' . time());
    }

    /**
     * Retrieve domain url
     * @return string
     */
    public function getDomain()
    {
        return $this->getAdditionalDataValue('domain');
    }

    /**
     * Retrieve tracking type code
     * @return string
     */
    public function getTrackingEvent()
    {
        return $this->getAdditionalDataValue('tracking_event', '0');
    }

    /**
     * Retrieve tracking parameter
     * @var int
     * @return string
     */
    public function getTrackingParameter($id)
    {
        return $this->getAdditionalDataValue('tracking_parameter' . $id);
    }

    /**
     * Retrieve tracking type code
     * @return string
     */
    public function getTrackingAttributeBrand()
    {
        return $this->getAdditionalDataValue('tracking_attribute_brand');
    }

    /**
     * Retrieve tracking attribute
     * @var int
     * @return string
     */
    public function getTrackingAttribute($id)
    {
        return $this->getAdditionalDataValue('tracking_attribute' . $id);
    }

    /**
     * Retrieve tracking type code
     *
     * @return array
     */
    public function getProfilingEvent()
    {
        $additionalData = $this->getAdditionalDataArray();

        return (isset($additionalData['profiling_event']) && is_array($additionalData['profiling_event']))
            ? $additionalData['profiling_event']
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = "";

        if ($_section == parent::SECTION_BODYEND) {
            $programId = $this->getProgramId();
            $domainInfo = $this->domain->getDataByCode(
                $this->getDomain()
            );

            if (!$domainInfo || !$programId) {
                return $html;
            }

            foreach ($_includeon as $position) {
                if ($position == 'all') {
                    continue;
                }

                $pixelData = $this->_getPixelData($position);

                if (count($pixelData)) {
                    if (!empty($domainInfo['script'])) {
                        /* Script Pixel */
                        $html .= "<script>
                            (function (w, d, namespace, domain, progId, tagId, undefined) {
                                w[namespace] = w[namespace] || {};
                                var act = w[namespace], payload = [];
                                act.tags = act.tags || [];
                                act.tags.push(tagId, payload);
                                var protocol = d.location.protocol;
                            ";

                        foreach ($pixelData as $moduleData) {
                            $html .= "      payload.push(" . json_encode($moduleData) . ");";
                        }

                        $html .= "
                                if (act.get === undefined) {
                                    var s = d.createElement('script');
                                    s.type = 'text/javascript';
                                    s.src = protocol + '//' + domain + '/' + 'affadvc.aspx?ns=' + namespace + '&dm=' + domain + '&site=' + progId + '&tag=' + tagId;
                                    s.async = false;
                                    (d.getElementsByTagName('body')[0] || d.getElementsByTagName('head')[0]).appendChild(s);
                                }else { act.get(w, d, progId, tagId); }
                            })(window, document, 'aff_act_1.0',
                                '" . $domainInfo['script'] . "',
                                '" . $this->getProgramId() . "',
                                '" . $this->getTagId() . "'
                            );
                        </script>";
                    }
                } // if valid data
            }
        }

        return $html;
    }

    protected function _getPixelData($position)
    {
        $data = [];

        /* Order Tracking Module */
        if (($position == self::CHECKOUT_SUCCESS_PAGE)
            && ($this->getTrackingEvent() != '0')
            && ($trackingData = $this->_getTrackingData())
        ) {
            $data[] = $trackingData;
        }

        /* Profiling Module */
        if (!empty($position)
            && count($this->getProfilingEvent())
            && ($profilingData = $this->_getProfilingData($position))
        ) {
            $data[] = $profilingData;
        }

        return $data;
    }

    protected function _isValidData($data, $requiredParams = false)
    {
        if (is_array($requiredParams)) {
            foreach ($requiredParams as $parameter) {
                if (!array_key_exists($parameter, $data)) {
                    return false;
                }

                if ((is_array($data[$parameter]) && !count($data[$parameter]))
                    || (is_string($data[$parameter]) && ($data[$parameter] === ''))
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function _getTrackingData()
    {
        $eventCode = $this->getTrackingEvent();
        $events = $this->trackingEvent->toOptionArray();

        $order = $this->getLastOrder();

        if (!empty($events[$eventCode]) && $order && $order->getId()) {
            /* Fixed parameters */
            $data = [
                'module' => self::MODULE_ORDER_TRACKING,
                'event' => $events[$eventCode]['name'],
                'order_id' => $order->getIncrementId(),
                'net_price' => $this->_formatAmount($order->getGrandTotal()),
                'rate_number' => $events[$eventCode]['rate_number'],
                'currency' => $this->getCurrencyCode($order),
                /* 'order_description' => 'Test Order Description', */
                'product_ids' => $this->_getProductIds($order),
            ];

            $data = array_merge($data, $this->_getProgramSubIds($order));

            if ($voucher = $order->getCouponCode()) {
                $data['voucher_code'] = $this->escapeValue($voucher);
            }

            switch ($eventCode) {
                /* Sale Tracking */
                case 1:
                    break;

                /* Lead Tracking */
                case 2:
                    $data['lmulti'] = 4;
                    break;

                /* Basket Tracking */
                case 3:
                    $data['basket_items'] = $this->_getBasketItems($order);
                    break;

                default:
            }

        }

        /* Validating data before adding into pixel */
        $requiredParams = array_key_exists('required', $events[$eventCode])
            ? $events[$eventCode]['required']
            : false;

        return $this->_isValidData($data, $requiredParams) ? $data : [];
    }

    protected function _getProfilingData($position)
    {
        $allowedEvents = $this->getProfilingEvent();
        $events = $this->profilingEvent->toOptionArray();

        $currentEvent = (array_key_exists($position, $events)
            && !empty($events[$position]['value'])
        )
            ? $events[$position]['value']
            : false;

        if (!$currentEvent) {
            return [];
        }

        $data = [
            'module' => self::MODULE_PROFILING,
            'event' => $currentEvent,
        ];

        switch ($position) {
            case self::HOME_PAGE:
                $titleBlock = $this->layoutInterface->getBlock('page.main.title');

                if ($titleBlock) {
                    $data['page_name'] = $this->escapeValue(
                        $titleBlock->getPageTitle()
                    );
                }

                $data['page_category'] = 'general';
                $data['page_type'] = 'default';
                $data['page_url'] = $this->escapeValue(
                    $this->_url->getCurrentUrl()
                );

                break;

            case self::PRODUCT_PAGE:
                $product = $this->_registry->registry('current_product');
                if ($product && $product->getId()) {
                    $store = $this->_storeManager->getStore();
                    if ($store && $store->getId()) {
                        $data['currency'] = $store->getBaseCurrencyCode();
                    }

                    $data['product_id'] = $product->getSku();
                    $data['product_name'] = $this->escapeValue($product->getName());
                    $data['product_price'] = $this->_formatAmount($product->getFinalPrice());

                    $productCategory = [];
                    if ($categoryId = $product->getCategoryId()) {
                        $category = $this->_categoryFactory->create()->load($categoryId);
                        $productCategory = $this->_getFullCategoryPath($category, null, true);
                    } else {
                        $productCategory = $this->_getProductCategory($product, null, true);
                    }
                    $data['product_category'] = $productCategory;
                    $data['product_click_url'] = $this->escapeValue($product->getProductUrl());
                }
                break;

            case self::CATEGORY_PAGE:
                $category = $this->_registry->registry('current_category');
                if ($category && $category->getId()) {
                    $data['category_name'] = $this->escapeValue($category->getName());
                    $data['category_id'] = (int)$category->getId();
                    $data['category_click_url'] = $this->escapeValue($category->getUrl());

                    if ($imageUrl = $category->getImageUrl()) {
                        $data['category_img_url'] = $imageUrl;
                    }
                }
                break;

            case self::CART_PAGE:
                $quote = $this->_checkoutSession->getQuote();

                if ($quote && $quote->getId()) {
                    $store = $this->_storeManager->getStore();
                    if ($store && $store->getId()) {
                        $data['currency'] = $store->getBaseCurrencyCode();
                    }

                    $data['products'] = [];
                    foreach ($quote->getAllVisibleItems() as $item) {
                        $data['products'][] = [
                            'product_id' => $item->getSku(),
                            'product_name' => $this->escapeValue(
                                $item->getName()
                            ),
                            'product_price' => $this->_formatAmount(
                                $this->_getProductSinglePrice($item)
                            ),
                            'product_quantity' => round(
                                $item->getQtyOrdered()
                                    ? $item->getQtyOrdered()
                                    : $item->getData('qty')
                            ),
                        ];
                    }
                }
                break;

            case self::CHECKOUT_SUCCESS_PAGE:
                $order = $this->getLastOrder();

                if ($order && $order->getId()) {
                    $store = $this->_storeManager->getStore();
                    if ($store && $store->getId()) {
                        $data['currency'] = $store->getBaseCurrencyCode();
                    }

                    $data['order_id'] = $order->getIncrementId();
                    $orderTotal = $order->getGrandTotal();
                    if ($shippingAmount = $order->getShippingAmount()) {
                        $orderTotal = $orderTotal - $shippingAmount;
                    }
                    $data['order_total_gross_price'] = $this->_formatAmount($orderTotal);

                    $orderItems = $order->getAllVisibleItems();
                    $data['order_total_items'] = count($orderItems);

                    $data['products'] = [];
                    foreach ($orderItems as $item) {
                        $data['products'][] = [
                            'product_id' => $item->getSku(),
                            'product_name' => $this->escapeValue(
                                $item->getName()
                            ),
                            'product_price' => $this->_formatAmount(
                                $this->_getProductSinglePrice($item)
                            ),
                            'product_quantity' => round(
                                $item->getQtyOrdered()
                                    ? $item->getQtyOrdered()
                                    : $item->getData('qty')
                            ),
                        ];
                    }
                }
                break;

            case self::SEARCH_RESULT_PAGE:
                $data['search_keywords'] = [];
                $data['products'] = [];
                $queryString = $this->catalogSearchHelper->getEscapedQueryText();

                if (!empty($queryString)) {
                    $queryKeywords = explode(' ', $queryString);
                    if (is_array($queryKeywords)) {
                        foreach ($queryKeywords as $word) {
                            $data['search_keywords'][] = $this->escapeValue($word);
                        }
                    }
                }

                $productListBlock = $this->layoutInterface->getBlock('search_result_list');

                if ($productListBlock) {
                    $productCollection = $productListBlock->getLoadedProductCollection();
                    foreach ($productCollection as $item) {
                        $data['products'][] = $this->escapeValue($item->getName());
                    }
                }

                break;

            default:
        }

        /* Validating data before adding into pixel */
        $requiredParams = array_key_exists('required', $events[$position])
            ? $events[$position]['required']
            : false;

        return $this->_isValidData($data, $requiredParams) ? $data : [];
    }

    /**
     * Retrieve encoded value
     * Encode according to RFC 3986 (security reasons for requests)
     * Returns a string in which all non-alphanumeric characters except -_.~
     * have been replaced with a percent (%) sign followed by two hex digits.
     * @param  mixed $value
     * @return string
     */
    public function escapeValue($value)
    {
        return rawurlencode($value);
    }

    /**
     * Format amount
     * @param  float $amount
     * @return string
     */
    protected function _formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     *  Retrieve price of single product
     * @param Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    protected function _getProductSinglePrice($item)
    {
        $qty = $item->getQtyOrdered()
            ? $item->getQtyOrdered()
            : $item->getData('qty');

        if (!$qty) {
            return 0;
        }

        return ($item->getBaseRowTotal()
            + $item->getBaseTaxAmount()
            + $item->getBaseHiddenTaxAmount()
            + $item->getBaseWeeeTaxAppliedRowAmount()
            - $item->getBaseDiscountAmount()
        ) / $qty;
    }

    /**
     * Retrieve additional info about payment and shipping method
     * @param int $index
     * @return string
     */
    protected function _getSubInfo($order, $index)
    {

        if ($order && $parameter = $this->getTrackingParameter($index)) {
            switch ($parameter) {
                case TrackingParameter::PAYMENT_METHOD:
                    $payment = $order->getPayment();
                    return ($payment && $payment->getMethodInstance())
                        ? $order->getPayment()->getMethodInstance()->getTitle()
                        : '';
                case TrackingParameter::SHIPPING_METHOD:
                    return $order->getShippingDescription();
                default:
            }
        }
        return '';
    }

    public function _getProgramSubIds($order)
    {
        $result = [];

        if ($order) {
            for ($i=1; $i <= TrackingParameter::MAX_COUNT; $i++) {
                if ($info = $this->_getSubInfo($order, $i)) {
                    $result['program_subid' . $i] = $this->escapeValue($info);
                }
            }
        }

        return $result;
    }

    protected function _getProductIds($order)
    {
        $result = [];

        if ($order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $product = $this->_productFactory->create()->load($item->getProductId());
                $result[] = $item->getSku();
            }
        }

        return $result;
    }

    protected function _getBasketItems($order)
    {
        $result = [];

        if ($order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $product = $this->_productFactory->create()->load($item->getProductId());
                $itemData = [
                    'product_id' => $item->getSku(),
                    'product_price' => $this->_formatAmount(
                        $this->_getProductSinglePrice($item)
                    ),
                    'product_quantity' => round($item->getQtyOrdered()),
                ];

                /* Product Category */
                $productCategory = '';
                if ($categoryId = $product->getCategoryId()) {
                    $category = $this->_categoryFactory->create()->load($categoryId);
                    $productCategory = $this->_getFullCategoryPath($category);
                } else {
                    $productCategory = $this->_getProductCategory($product);
                }

                if ($productCategory) {
                    $itemData['product_category'] = $this->escapeValue($productCategory);
                }

                /* Produt Brand */
                $productManufacturer = '';
                $manufacturerAttribute = $this->getTrackingAttributeBrand();

                if ($manufacturerAttribute && $product->getData($manufacturerAttribute)) {
                    $manufacturerAttributeModel = $product->getResource()->getAttribute($manufacturerAttribute);

                    $productManufacturer = (is_callable(array($manufacturerAttributeModel, 'getFrontend')))
                        ? $this->escapeValue($manufacturerAttributeModel->getFrontend()->getValue($product))
                        : $productManufacturer = $this->escapeValue($product->getData($manufacturerAttribute));
                }

                if ($productManufacturer) {
                    $itemData['product_brand'] = $this->escapeValue($productManufacturer);
                }

                /* Produt Name */
                if ($productName = $item->getName()) {
                    $itemData['product_name'] = $this->escapeValue($productName);
                }

                /* Product Properties */
                $trackingAttributes = [];
                for ($i=1; $i<=ProductAttribute::MAX_COUNT; $i++) {
                    $attributeValue = trim($this->getTrackingAttribute($i));

                    if ($attributeValue) {
                        $trackingAttributes[$i] = $attributeValue;
                    }
                }

                foreach ($trackingAttributes as $index => $attributeCode) {
                    $propertyKey = 'product_property' . $index;
                    if ($attributeCode && $product->getData($attributeCode)) {
                        $attributeModel = $product->getResource()->getAttribute($attributeCode);

                        if (is_callable(array($attributeModel, 'getFrontend'))) {
                            $propertyValue = $attributeModel->getFrontend()->getValue($product);
                        } else {
                            $propertyValue = $product->getData($attributeCode);
                        }

                        $itemData[$propertyKey] = $this->escapeValue($propertyValue);
                    }
                }

                $result[] = $itemData;
            }
        }

        return $result;
    }

    /**
     * Retrieve product category path
     * @param  Magento\Catalog\Model\Product $product
     * @param  string $separator
     * @return string
     */
    protected function _getProductCategory($product, $separator = ' > ', $toArray = false)
    {
        $categoryIds = $product->getCategoryIds();
        $collection = $this->categoryCollection->create();
        $collection->addAttributeToFilter('entity_id', array('in' => $categoryIds))
            ->setOrder('level', 'desc')
            ->load();

        /* @todo check compatibility with flat catalog */
        if ($collection->count()) {
            return $this->_getFullCategoryPath($collection->getFirstItem(), $separator, $toArray);
        }

        return  $toArray ? [] : '';
    }

    /**
     * Retrieve product category full path
     * @param  Category $category
     * @param  string $separator
     * @return string
     */
    protected function _getFullCategoryPath($category, $separator = '|', $toArray = false)
    {
        if (is_object($category) && $category->getId()) {
            $path = explode('/', $category->getPath());

            $collection = $this->categoryCollection->create();

            $collection->addAttributeToSelect('name')
                ->addAttributeToFilter('entity_id', array('in' => $path))
                ->setOrder('level', 'asc');

            $categoryName = array();
            $store = $this->_storeManager->getStore();

            if ($store && $store->getId() && ($rootCategoryId = $store->getRootCategoryId())) {
                foreach ($collection as $pathCategory) {
                    if (($rootCategoryId != $pathCategory->getId()) && ($pathCategory->getId() != 1)) {
                        $categoryName[] = $this->escapeValue($pathCategory->getName());
                    }
                }

                if ($toArray) {
                    return $categoryName;
                }

                $categoryName = implode($separator, $categoryName);
                return $categoryName;
            }
        }

        return  $toArray ? [] : '';
    }
}
