<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class PowerReviews extends AbstractModel
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * PowerReviews constructor.
     *
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
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
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
        $this->escaper = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function getMerchantGroupId()
    {
        return $this->getAdditionalDataValue('merchant_group_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->getAdditionalDataValue('locale');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;

        if ($_section == parent::SECTION_BODYBEGIN) {
            $html .= '<script type="text/javascript" src="//static.powerreviews.com/t/v1/tracker.js"></script>';
        } elseif ($_section == parent::SECTION_BODYEND) {
            /**
             * @var \Magento\Sales\Model\Order $order
             */
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $products = [];
                foreach ($order->getAllVisibleItems() as $item) {
                    /** @var \Magento\Sales\Model\Order\Item $item */
                    $_product = $item->getProduct();
                    if (!$_product) {
                        $_product = $this->_productFactory->create()->load($item->getProductId());
                        $item->setProduct($_product);
                    }

                    $products[] = '{
                            page_id: \'' . $item->getSku() . '\',
                            product_name: \'' . $this->escaper->escapeJs($item->getName()) . '\',
                            quantity: ' . (int)$item->getQtyOrdered() . ',
                            unit_price: ' . round($item->getPrice(), 2) . '
                        }';
                }

                $order->getIncrementId();

                $html .= '<script type="text/javascript">
                    (function(){try{
                    var tracker = POWERREVIEWS.tracker.createTracker({merchantGroupId: "' . $this->getMerchantGroupId() . '"});

                    var orderFeed = {
                        merchantGroupId: "' . $this->getMerchantGroupId() . '",
                        merchantId: "' . $this->getMerchantId() . '",
                        locale: "' . $this->getLocale() . '",
                        merchantUserId: "' . $order->getCustomerId() . '",
                        marketingOptIn: true,
                        userEmail: "' . $order->getCustomerEmail() . '",
                        userFirstName: "' . $order->getCustomerFirstname() . '",
                        userLastName: "' . $order->getCustomerLastname() . '",
                        orderId: "' . $order->getIncrementId() . '",
                        orderSubtotal: ' . round($order->getSubtotal(), 2) . ',
                        orderItems: [' . implode(',', $products) . ']
                    }

                    tracker.trackCheckout(orderFeed);

                }catch(e){window.console && window.console.log(e)}}());
                </script>';
            }
        }

        return $html;
    }
}
