<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

use Magento\Catalog\Model\Product;
use Magento\Framework\Encryption\Encryptor;

class WebGains extends AbstractModel
{
    /**
     * Save categories mapping
     * @var array
     */
    protected $categoriesEventId = [];

    /**
     * Save products mapping
     * @var array
     */
    protected $productsEventId = [];

    /**
     * Product Repo
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Category Repo
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * WebGains constructor.
     *
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
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param null $resource
     * @param null $resourceCollection
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
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        $resource = null,
        $resourceCollection = null,
        array $data = []
    ) {
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
        $this->productRepository  = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->encryptor = $encryptor;
    }

    /**
     * Get program id
     *
     * @return int
     */
    public function getProgramId()
    {
        return $this->getAdditionalDataValue('program_id');
    }

    /**
     * Get event id
     *
     * @return int
     */
    public function getEventId($item = null)
    {
        $eventId = '';

        // from product
        if ($item) {
            $eventId = $this->getEventIdByProductId($item->getProductId());
        }

        // from config
        if (! $this->isValidEventId($eventId)) {
            $eventId = $this->getAdditionalDataValue('event_id');
        }

        return $eventId;
    }

    /**
     * Validate event Id
     *
     * @param  mixed $eventId
     * @return boolean
     */
    protected function isValidEventId($eventId)
    {
        return ($eventId !== null && $eventId !== '');
    }

    /**
     * Retrieve Event Id from product
     *
     * @param $productId
     * @return mixed
     */
    protected function getEventIdByProductId($productId)
    {
        if ($productId) {
            if (!isset($this->productsEventId[$productId])) {
                /**
                 * @var $product Product
                 */
                $product = $this->productRepository->getById($productId);
                $this->productsEventId[$productId] = $product->getData('affiliate_webgains_eventid');
            }
        }

        // from categories
        if (isset($product) && !$this->isValidEventId($this->productsEventId[$productId])) {
            $this->productsEventId[$productId] = $this->getEventIdByCategoryIds($product->getCategoryIds());
        }

        return $this->productsEventId[$productId];
    }

    /**
     * Get event id from categories
     *
     * @param array $categoryIds
     * @return int | null
     */
    protected function getEventIdByCategoryIds($categoryIds)
    {
        $eventId = null;
        if (count($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                if (!isset($this->categoriesEventId[$categoryId])) {
                    $category = $this->categoryRepository->get($categoryId);
                    $this->categoriesEventId[$categoryId] = $category->getData('affiliate_webgains_eventid');
                }
                if ($this->isValidEventId($this->categoriesEventId[$categoryId])) {
                    $eventId = $this->categoriesEventId[$categoryId];
                    break;
                }
            }
        }

        return $eventId;
    }

    /**
     * Get pin
     *
     * @return int
     */
    public function getPin()
    {
        return $this->getAdditionalDataValue('pin_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;
        $scheme = $this->_request->getScheme();

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $coupon = $order->getCouponCode() ? $order->getCouponCode() : '';

                /* wgitems - (optional) should contain pipe separated list of shopping basket items. Fields for each item are seperated by double colon.
                    First field is commission type, second field is price of item, third field (optional) is name of item, fourth field (optional) is product code/id, fifth field (optional) is voucher code. Example for two items; items=1::54.99::Harry%20Potter%20dvd::hpdvd93876|5::2.99::toothbrush::tb287::voucher1    */
                $products = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    $itemStr = implode('::', [
                        $this->getEventId($item), // Event ID
                        round($item->getPrice(), 2), // Product Price
                        rawurlencode($item->getName()), // Product Name
                        rawurlencode($item->getSku()), // Product ID
                        rawurlencode($coupon), // Voucher Code
                    ]);
                    $qtyOrdered = (int)$item->getQtyOrdered();

                    for ($qty = 1; $qty <= $qtyOrdered; $qty++) {
                        $products[] = $itemStr;
                    }
                }

                if ($products && $coupon) {
                    $products[] = implode('::', [
                        $this->getEventId(), // Event ID
                        round($order->getBaseDiscountAmount(), 2), // Discount
                        rawurlencode($coupon), // Voucher Code
                        rawurlencode($coupon), // Voucher Code
                    ]);
                }

                $amount = round($order->getBaseSubtotal() - abs($order->getBaseDiscountAmount()), 2);

                $params = [
                    'wgver'             => '1.2',
                    'wgsubdomain'       => 'track',
                    'wglang'            => $this->getLocaleCode(),
                    'wgslang'           => 'php',
                    'wgprogramid'       => $this->getProgramId(),
                    'wgeventid'         => $this->getEventId(),
                    'wgvalue'           => $amount,
                    'wgorderreference'  => rawurlencode($order->getIncrementId()),
                    'wgcomment'         => '',
                    'wgmultiple'        => '1',
                    'wgitems'           => implode('|', $products),
                    'wgcustomerid'      => '', // please do not use without contacting us first
                    'wgproductid'       => '', // please do not use without contacting us first
                    'wgvouchercode'     => rawurlencode($coupon),
                ];

                $hash = $this->encryptor->hash($this->getPin() . implode('&', $params), Encryptor::HASH_VERSION_MD5);

                $params = array_merge(
                    $params,
                    [
                        'wgchecksum'        => $hash,
                        'wgrs'              => '1',
                        'wgprotocol'        => $scheme,
                        'wgcurrency'        => $this->getCurrencyCode($order),
                    ]
                );

                $html = '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                                <img src="'. $scheme .'://'. $params['wgsubdomain'] .'.webgains.com/transaction.html?'. urldecode(http_build_query($params)) .'" width="1" height="1" />
                            </div>';
            }
        }

        return $html;
    }
}
