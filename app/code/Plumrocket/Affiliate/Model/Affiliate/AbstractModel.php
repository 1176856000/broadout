<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

/**
 * @since 2.0.0
 * @deprecated since 2.8.0 - has too much responsibility and dependencies
 * @see \Plumrocket\Affiliate\Model\Affiliate\AbstractDataModel
 */
abstract class AbstractModel extends AbstractDataModel
{
    const ENABLED_STATUS        = 'ENABLED';
    const DISABLED_STATUS       = 'DISABLED';

    /**
     * Page section
     * @var string
     */
    protected $_pageSections    = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $_cookieManager;

    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

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
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_customerSession = $customerSession;
        $this->_dataHelper = $dataHelper;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_remoteAddress = $remoteAddress;
        $this->_imageHelper = $imageHelper;
        $this->_scopeConfigInterface = $scopeConfigInterface;
        $this->_url = $url;
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_regionFactory = $regionFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get last order
     * @return \Magento\Sales\Model\Order
     */
    public function getLastOrder()
    {
        if (!$this->_registry->registry('current_order')) {
            $this->_registry->register('current_order', $this->_checkoutSession->getLastRealOrder());
        }
        return $this->_registry->registry('current_order');
    }

    /**
     * Get merchant id
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getConfig('merchant_id');
    }

    /**
     * Is cps enabled
     * @return bool
     */
    public function getCpsEnabled()
    {
        return (bool) $this->getConfig('cps_enable');
    }

    /**
     * Is cpl enabled
     * @return bool
     */
    public function getCplEnabled()
    {
        return (bool) $this->getConfig('cpl_enable');
    }

    /**
     * Get additional data value
     *
     * @param string $key
     * @param array|string|int|null $default
     * @return array|string|int|null
     *
     * @deprecated since 2.8.0
     * @see \Plumrocket\Affiliate\Model\Affiliate\AbstractDataModel::getConfig
     */
    public function getAdditionalDataValue(string $key, $default = '')
    {
        return $this->getConfig($key, $default);
    }

    /**
     * Get renderer code
     * @param  string $_section
     * @return string           html
     */
    protected function _getRenderedCode($_section, $_includeon = null)
    {
        return '';
    }

    /**
     * Get current locale code
     * @param  int $storeId
     * @return string
     */
    public function getLocaleCode($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        return $this->_scopeConfigInterface->getValue(
            \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get page section
     * @return array
     */
    public function getPageSections()
    {
        if ($this->_pageSections === null) {
            $this->_pageSections = [
                [
                    'key'   => self::SECTION_HEAD,
                    'lable' => __('Script in &#60;HEAD&#62; section'),
                ],
                [
                    'key'   => self::SECTION_BODYBEGIN,
                    'lable' => __('Script after &#60;BODY&#62; opening tag'),
                ],
                [
                    'key'   => self::SECTION_BODYEND,
                    'lable' => __('Script before &#60;/BODY&#62; closing tag'),
                ],
            ];
        }
        return $this->_pageSections;
    }

    /**
     * Get library html
     *
     * Use getHeadLibrary, getBodybeginLibrary and getBodyendLibrary models methods
     *
     * @deprecated since 2.8.0 - methods getHeadLibrary and other are not implements anywhere
     * @param  string $_section
     * @return string
     */
    public function getLibraryHtml($_section)
    {
        $getSectionLibrary = 'getSection'.ucfirst($_section).'Library';

        if ($lib = $this->$getSectionLibrary()) {
            $mediaUrl = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

            return '<script type="text/javascript" src="'.$mediaUrl.$lib.'"></script>'."\n\r";
        }
        return null;
    }

    /**
     * get code html
     * @param  string $_section
     * @param  array $_includeon
     * @return string
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $getSectionCode = 'getSection'.ucfirst($_section).'Code';
        if ($code = $this->$getSectionCode()) {
            return $code."\n\r";
        }
        return null;
    }

    /**
     * Get current currency core
     *
     * @param  \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getCurrencyCode($order)
    {
        $currencyCode = null;
        $currency = $order->getOrderCurrency();
        if (is_object($currency)) {
            $currencyCode = $currency->getCurrencyCode();
        }
        return $currencyCode;
    }

    /**
     * Is new customer
     * @param  \Magento\Sales\Model\Order  $order
     * @return boolean
     */
    public function isNewCustomer($order)
    {
        $collection = $this->_orderFactory->create()->getCollection()
            ->addFieldToFilter('entity_id', ['neq' => $order->getId()])
            ->addFieldToFilter('store_id', $order->getStoreId())
            ->setPageSize(1);

        if ($order->getCustomerId()) {
            $collection->getSelect()
                ->where(
                    '`customer_email` = "'. $order->getCustomerEmail() .'"
                    OR
                    `customer_id` = '. (int)$order->getCustomerId()
                );
        } else {
            $collection->addFieldToFilter('customer_email', $order->getCustomerEmail());
        }

        return !count($collection);
    }
}
