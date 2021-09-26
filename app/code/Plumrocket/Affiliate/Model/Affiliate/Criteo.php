<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */


namespace Plumrocket\Affiliate\Model\Affiliate;

class Criteo extends AbstractModel
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layoutInterface;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager              $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                              $dataHelper
     * @param \Magento\Framework\Model\Context                               $context
     * @param \Magento\Framework\Registry                                    $registry
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param \Magento\Checkout\Model\Session                                $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                        $request
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                          $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                              $orderFactory
     * @param \Magento\Directory\Model\RegionFactory                         $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress           $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                  $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface             $scopeConfigInterface
     * @param \Magento\Framework\Url                                         $url
     * @param \Magento\Framework\View\LayoutInterface                        $layoutInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null   $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null             $resourceCollection
     * @param array                                                          $data
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
        \Magento\Framework\View\LayoutInterface $layoutInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
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
     * Retrieve partner id
     *
     * @return string
     */
    public function getPartnerId()
    {
        return $this->getAdditionalDataValue('partner_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        if ($_section == parent::SECTION_BODYEND) {
            $htmlHead = '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
<script type="text/javascript">
    _CRITEO_SITE_TYPE = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";

    function getPrACookie(name) {
        var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, \'\\$1\') + "=([^;]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    _CUSTOMER_EMAIL_HASH = getPrACookie("cutomer_email_hash");
    if (!_CUSTOMER_EMAIL_HASH) _CUSTOMER_EMAIL_HASH = "";
</script>
<script type="text/javascript">
window.criteo_q = window.criteo_q || [];
window.criteo_q.push(
    { event: "setAccount", account: "' . $this->getPartnerId() . '" },
    { event: "setSiteType", type: _CRITEO_SITE_TYPE },
    { event: "setHashedEmail", email: _CUSTOMER_EMAIL_HASH },
    ';

            $html = '';
            $htmlFooter = '
);
</script>';

            switch (true) {
                case in_array('home_page', $_includeon):
                    $event = [
                        'event' => 'viewHome',
                    ];
                    $html = json_encode($event);
                    break;
                case in_array('product_page', $_includeon):
                    $product = $this->_registry->registry('current_product');
                    $event = [
                        'event' => 'viewItem',
                        'item' => $product->getSku(),
                    ];
                    $html = json_encode($event);
                    break;
                case in_array('category_page', $_includeon):
                    $productListBlock = $this->layoutInterface->getBlock('category.products.list');
                    if ($productListBlock) {
                        $event = [
                            'event' => 'viewList',
                            'item' => [],
                        ];
                        $products = $productListBlock->getLoadedProductCollection();
                        $i = 0;
                        foreach ($products as $product) {
                            $sku = $product->getSku();
                            if (!$sku) {
                                $_resource = $product->getResource();
                                $sku = $_resource->getAttributeRawValue($product->getId(), 'sku', $this->_storeManager->getStore());
                            }
                            $event['item'][] = $sku;
                            $i++;
                            if ($i >= 3) {
                                break;
                            }

                        }
                        $html = json_encode($event);
                    }
                    break;
                case in_array('cart_page', $_includeon):
                    $quote = $this->_checkoutSession->getQuote();
                    $items = $quote->getAllVisibleItems();
                    if (count($items)) {
                        $event = [
                            'event' => 'viewBasket',
                            'item' => [],
                        ];
                        foreach ($items as $item) {
                            $event['item'][] = [
                                'id' => $item->getSku(),
                                'price' => $item->getPrice(),
                                'quantity' => $item->getQty(),
                            ];
                        }
                        $html = json_encode($event);
                    }
                    break;
                case in_array('checkout_success', $_includeon):
                    $order = $this->getLastOrder();
                    if ($order && $order->getId()) {
                        $items = $order->getAllVisibleItems();
                        if (count($items)) {
                            $event = [
                                'event' => 'trackTransaction',
                                'id' => $order->getIncrementId(),
                                'item' => [],
                            ];
                            foreach ($items as $item) {
                                $event['item'][] = [
                                    'id' => $item->getSku(),
                                    'price' => $item->getPrice(),
                                    'quantity' => (int)$item->getQtyOrdered(),
                                ];
                            }

                            $html = json_encode($event);
                        }

                    }
                    break;
            }

            if ($html) {
                return $htmlHead . $html . $htmlFooter;
            }
        }
    }
}

