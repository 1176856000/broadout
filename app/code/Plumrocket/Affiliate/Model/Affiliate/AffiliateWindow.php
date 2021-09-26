<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Session\Config\ConfigInterface;
use Plumrocket\Affiliate\Model\Config\Source\AffiliateWindow\CommissionGroup;

class AffiliateWindow extends AbstractModel
{
    /**
     * Session config
     *
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    public $sessionConfig;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollectionFactory;

    /**
     * Save categories mapping
     * @var array
     */
    private $categoriesGroupCode = [];

    /**
     * Category Repo
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * AffiliateWindow constructor.
     *
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager             $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                             $dataHelper
     * @param \Magento\Framework\Model\Context                              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Customer\Model\Session                               $customerSession
     * @param \Magento\Checkout\Model\Session                               $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                       $request
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                         $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                        $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                             $orderFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory    $orderCollectionFactory
     * @param \Magento\Directory\Model\RegionFactory                        $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress          $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                 $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface            $scopeConfigInterface
     * @param \Magento\Framework\Url                                        $url
     * @param \Magento\Framework\HTTP\ClientInterface                       $curl
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface              $categoryRepository
     * @param ConfigInterface                                               $sessionConfig
     * @param null                                                          $resource
     * @param null                                                          $resourceCollection
     * @param array                                                         $data
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
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Url $url,
        \Magento\Framework\HTTP\ClientInterface $curl,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        ConfigInterface $sessionConfig,
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
        $this->sessionConfig = $sessionConfig;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->curl = $curl;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvertiserId()
    {
        return $this->getAdditionalDataValue('advertiser_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getActivateTrackingCode()
    {
        return $this->getAdditionalDataValue('activate_tracking_code', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlt()
    {
        $plt = $this->getAdditionalDataValue('plt');
        return ($this->getActivateTrackingCode() && $plt) ? $plt : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getTestMode()
    {
        $testMode = $this->getAdditionalDataValue('test_mode');
        return ($this->getActivateTrackingCode() && $testMode) ? $testMode : 0;
    }

    /**
     * @return null|string
     */
    public function getChanel()
    {
        if ($this->getAdditionalDataValue('enable_dedupe')) {
            $keyParam = $this->getAdditionalDataValue('param_key');
            if ($keyParam) {
                $channelParameter = $this->_cookieManager
                    ->getCookie($keyParam, $this->getAdditionalDataValue('default_value'));
                return $channelParameter;
            }
        }
        return 'aw';
    }

    /**
     * Validate Commission Group Code
     *
     * @param  mixed $groupId
     * @return boolean
     */
    protected function isValidGroupCode($groupId)
    {
        return ($groupId !== null && $groupId !== '');
    }

    /**
     * Get event id from categories
     *
     * @param $product
     * @return string | null
     */
    protected function getGroupCodeFromCategory($product)
    {
        $groupCode = null;
        $categoryIds = $product->getCategoryIds();

        if (!empty($categoryIds)) {
            $categories = $this->_categoryFactory->create()
                ->getCollection()
                ->addAttributeToSelect('affiliate_aw_commission_group')
                ->addFieldToFilter('entity_id', ["in" => $categoryIds])
                ->setOrder('level', CategoryCollection::SORT_ORDER_DESC);

            foreach ($categories as $category) {
                $categoryId = $category->getId();
                if (!isset($this->categoriesGroupCode[$categoryId])) {
                    $this->categoriesGroupCode[$categoryId] = $category->getData('affiliate_aw_commission_group');
                }

                if ($this->isValidGroupCode($this->categoriesGroupCode[$categoryId])) {
                    $groupCode = $this->categoriesGroupCode[$categoryId];
                    break;
                }
            }
        }

        return $groupCode;
    }

    /**
     * @param $product
     * @param $order
     * @return string
     */
    public function getCommissionGroupCode($product, $order)
    {
        $code = 'DEFAULT';
        if ($groupBy = $this->getAdditionalDataValue('commission_group')) {
            if ($groupBy == CommissionGroup::GROUP_PRODUCT) {
                $productCode = $product->getData('affiliate_aw_commission_group');
                if (!$this->isValidGroupCode($productCode)) {
                    $productCode = $this->getGroupCodeFromCategory($product);
                }
                if ($productCode) {
                    $code = $productCode;
                }
            } elseif ($groupBy == CommissionGroup::GROUP_CLIENT) {
                $code = 'NEW';
                if ($order && $order->getCustomerId()) {
                    $orderCollection = $this->orderCollectionFactory->create($order->getCustomerId());
                    if ($orderCollection->getSize() > 1) {
                        $code = 'EXISTING';
                    }
                }
            }
        }
        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;

        if ($_section == parent::SECTION_BODYBEGIN && $this->getActivateTrackingCode()) {
            if ($this->getAdditionalDataValue('enable_dedupe')) {
                $paramKey = $this->getAdditionalDataValue('param_key');

                $html .= "<script>
                    require([
                        'jquery',
                        'jquery/jquery.cookie'
                    ], function ($) {
                        var key = '".$paramKey."';
                        var regex = new RegExp('[?&]?' + key + '=([^&#]*|&|#|$)');
                        var results = regex.exec(window.location.href);
                        if (results != null) {
                            var cookieLength = '".$this->getAdditionalDataValue('cookie_length')."';
                            var cookiePath = '".$this->sessionConfig->getCookiePath()."';
                            var cookieDomain = '".$this->sessionConfig->getCookieDomain()."';
                            var date = new Date();
                            date.setTime(date.getTime() + (1000 * 60 * 60 * 24 * parseInt(cookieLength)));
                            $.cookie(key, results[1], {domain: cookieDomain, path: cookiePath, expires: date});
                        }
                    });
                    </script>";
            }

            /**
             * @var \Magento\Sales\Model\Order $order
             */
            $order = $this->getLastOrder();
            if ($order && $order->getId() && $order->getStatus() !== \Magento\Sales\Model\Order::STATE_CANCELED) {
                $pltHtml = '';
                $totalAmount = 0; //round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);
                $commissionGroups = [];

                // Product Level Tracking - Confirmation page.
                $products = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    $_product = $item->getProduct();
                    if (!$_product) {
                        $_product = $this->_productFactory->create()->load($item->getProductId());
                        $item->setProduct($_product);
                    }

                    $commissiongGroupCode = $this->getCommissionGroupCode($_product, $order);

                    $itemPrice = (($this->getAdditionalDataValue('tax_inclusive'))
                            ? $item->getPriceInclTax() : $item->getPrice());
                    if ($item->getDiscountAmount()) {
                        $itemPrice = $itemPrice - ($item->getDiscountAmount() / $item->getQtyOrdered());
                    }

                    if ($this->getPlt()) {
                        $productParams = [
                            'AW:P',
                            $this->getAdvertiserId(), // advertiserId
                            $order->getIncrementId(), // orderReference
                            $item->getSku(), // productId
                            $item->getName(), // productName
                            round($itemPrice, 2), // productItemPrice
                            round($item->getQtyOrdered()), // productQuantity
                            $item->getSku(), // productSku
                            $commissiongGroupCode, // commissionGroupCode
                            '', // productCategory
                        ];
                        $products[] = implode('|', $productParams);
                    }

                    if (!isset($commissionGroups[$commissiongGroupCode])) {
                        $commissionGroups[$commissiongGroupCode] = 0;
                    }

                    $price = (($this->getAdditionalDataValue('tax_inclusive') || $this->getAdditionalDataValue('taxes_inclusive'))
                            ? $item->getPriceInclTax()
                            : $item->getPrice()) * $item->getQtyOrdered() - $item->getDiscountAmount();

                    $commissionGroups[$commissiongGroupCode] += $price;
                    $totalAmount += $price;
                }

                if ($this->getAdditionalDataValue('delivery_cost_inclusive')) {
                    $totalAmount += $order->getShippingInclTax();
                }

                if ($this->getPlt()) {
                    $pltHtml = '
                        <form style="display: none;" name="aw_basket_form">
                            <textarea wrap="physical" id="aw_basket">'. implode("\r\n", $products) .'</textarea>
                        </form>';
                }

                $parts = [];
                foreach ($commissionGroups as $commissiongGroupCode => $commissionGroupAmount) {
                    $parts[] = $commissiongGroupCode . ':' . round($commissionGroupAmount, 2);
                }
                $parts = implode('|', $parts);

                // Conversion Tag - Confirmation page.
                $html .= '<script type="text/javascript">
                        //<![CDATA[
                        /*** Do not change ***/
                        var AWIN = {};
                        AWIN.Tracking = {};
                        AWIN.Tracking.Sale = {};
                        /*** Set your transaction parameters ***/
                        AWIN.Tracking.Sale.amount = "'. round($totalAmount, 2) .'";
                        AWIN.Tracking.Sale.channel = "'. $this->getChanel() .'";
                        AWIN.Tracking.Sale.currency = "'. $order->getOrderCurrencyCode() .'";
                        AWIN.Tracking.Sale.orderRef = "'. $order->getIncrementId() .'";
                        AWIN.Tracking.Sale.parts = "'. $parts .'";
                        AWIN.Tracking.Sale.test = "'. $this->getTestMode() .'";
                        AWIN.Tracking.Sale.voucher = "' . $order->getCouponCode() . '";
                        //]]>
                        </script>';

                // Fall-back Conversion Pixel - Confirmation page.
                $params = [
                    'tt'        => 'ns',
                    'tv'        => '2',
                    'merchant'  => $this->getAdvertiserId(),
                    'amount'    => round($totalAmount, 2),
                    'ch'        => $this->getChanel(),
                    'cr'        => $order->getOrderCurrencyCode(),
                    'parts'     => $parts,
                    'ref'       => $order->getIncrementId(),
                    'testmode'  => $this->getTestMode(),
                    'vc'        => $order->getCouponCode(),
                ];
                $html .= '
                    <div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                        <img src="https://www.awin1.com/sread.img?'.http_build_query($params).'" width="1" height="1" />
                    </div>';
                $awc = $this->_cookieManager->getCookie('awc');
                if ($awc) {
                    $this->serverToServerRequest($params, $awc);
                }

                $html .= $pltHtml;
            }
        } elseif ($_section == parent::SECTION_BODYEND && $this->getActivateTrackingCode()) {
            // Journey Tag / Mastertag - All pages.
            $html = '<script defer="defer" src="https://www.dwin1.com/'. $this->getAdvertiserId() .'.js" type="text/javascript"></script>';
        }

        return $html;
    }

    public function serverToServerRequest($params, $awc)
    {
        $params['tt'] = 'ss';
        $params['cks'] = $awc;
        $url = 'https://www.awin1.com/sread.php?' . http_build_query($params);
        $this->curl->get($url);
    }
}
