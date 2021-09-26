<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Model\Affiliate;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\Repository as AssertRepository;
use Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface;
use Plumrocket\Affiliate\Api\Data\PageTypeInterface;

/**
 * Everflow Affiliate Program
 *
 * Official site
 * @link https://www.everflow.io/
 *
 * Integration supports click and conversion tracking
 * @link https://developers.everflow.io/docs/everflow-sdk/
 *
 * @since 2.8.0
 */
class Everflow extends AbstractDataModel implements AffiliateProgramInterface
{
    public const INTEGRATION_TYPE_ORDER = 'order';
    public const INTEGRATION_TYPE_SKU = 'sku';

    /**
     * @var AssertRepository
     */
    private $assetRepo;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\Affiliate\Model\Affiliate\Pixel\JsRenderer
     */
    private $jsRenderer;

    /**
     * @var \Plumrocket\Affiliate\Model\Affiliate\Pixel\OrderProvider
     */
    private $orderProvider;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param AssertRepository                                             $assetRepo
     * @param \Magento\Framework\App\RequestInterface                      $request
     * @param \Plumrocket\Affiliate\Model\Affiliate\Pixel\JsRenderer       $jsRenderer
     * @param \Plumrocket\Affiliate\Model\Affiliate\Pixel\OrderProvider    $orderProvider
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AssertRepository $assetRepo,
        RequestInterface $request,
        Pixel\JsRenderer $jsRenderer,
        Pixel\OrderProvider $orderProvider,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->jsRenderer = $jsRenderer;
        $this->orderProvider = $orderProvider;
    }

    /**
     * @inheritDoc
     */
    public function getTypeId(): int
    {
        return (int) $this->getData('type_id');
    }

    /**
     * @return string
     */
    public function getConversionTrackingDomain(): string
    {
        return (string) $this->getConfig('conversion_tracking_domain');
    }

    /**
     * @return string
     */
    public function getClickTrackingDomain(): string
    {
        return (string) $this->getConfig('click_tracking_domain');
    }

    /**
     * @return int
     */
    public function getAdvertiserId(): int
    {
        return (int) $this->getConfig('advertiser_id');
    }

    /**
     * @return string
     */
    public function getIntegrationType(): string
    {
        return (string) $this->getConfig('integration_type', self::INTEGRATION_TYPE_ORDER);
    }

    /**
     * Direct linking make using click tracking possible
     *
     * @return bool
     */
    public function isDirectLinkingEnabled(): bool
    {
        return (bool) $this->getConfig('direct_linking', true);
    }

    /**
     * @return array
     */
    public function getClickParamsMapping(): array
    {
        $clickParamsMapping = $this->getConfig('click_params_mapping', []);

        return array_merge(
            [
                'offer_id' => 'oid',
                'affiliate_id' => 'affid',
                'sub1' => 'sub1',
                'sub2' => 'sub2',
                'sub3' => 'sub3',
                'sub4' => 'sub4',
                'sub5' => 'sub5',
                'uid' => 'uid',
                'source_id' => 'source_id',
                'transaction_id' => '_ef_transaction_id',
                'coupon_code' => 'coupon_code',
            ],
            $clickParamsMapping
        );
    }

    /**
     * @inheritDoc
     */
    public function getSectionPixelHtml(string $pageSection, array $pageTypes): string
    {
        switch ($pageSection) {
            case self::SECTION_HEAD:
                return $this->getSdkScript();
            case self::SECTION_BODYEND:
                if (in_array(PageTypeInterface::CHECKOUT_SUCCESS_PAGE, $pageTypes, true)) {
                    return $this->getConversionPixel();
                }
                return $this->getClickPixel();
            default:
                return '';
        }
    }

    /**
     * Get official SDK script
     *
     * Source
     * @link https://www.serve-eflow-test.com/scripts/sdk/everflow.js
     *
     * @return string
     */
    private function getSdkScript(): string
    {
        $url = $this->assetRepo->getUrlWithParams(
            'Plumrocket_Affiliate::js/everflow.js',
            ['_secure' => $this->request->isSecure()]
        );
        return '<script type="text/javascript" src="' . $url . '"></script>';
    }

    /**
     * Set tracking/conversion domain
     *
     * @param string $domain
     * @return string
     */
    private function getConfigureDomainJs(string $domain): string
    {
        return "EF.configure({tracking_domain: '$domain'});";
    }

    /**
     * Recommended pixel
     *
     * EF.click({
     *     offer_id: EF.urlParameter('oid'),
     *     affiliate_id: EF.urlParameter('affid'),
     *     sub1: EF.urlParameter('sub1'),
     *     sub2: EF.urlParameter('sub2'),
     *     sub3: EF.urlParameter('sub3'),
     *     sub4: EF.urlParameter('sub4'),
     *     sub5: EF.urlParameter('sub5'),
     *     uid: EF.urlParameter('uid'),
     *     source_id: EF.urlParameter('source_id'),
     *     transaction_id: EF.urlParameter('_ef_transaction_id'),
     *     coupon_code: EF.urlParameter('coupon_code')
     * });
     *
     * @return string
     */
    private function getClickPixel(): string
    {
        $pixel = $this->getConfigureDomainJs($this->getClickTrackingDomain()) . PHP_EOL;

        $pixel .= 'EF.click({';
        foreach ($this->getClickParamsMapping() as $key => $paramName) {
            $pixel .= "$key: EF.urlParameter('$paramName'),";
        }
        $pixel = rtrim($pixel, ',');
        $pixel .= '});';

        return $this->jsRenderer->wrapJsCode($pixel);
    }

    /**
     * There are two possible types of conversion pixel:
     *
     * 1. When we track whole order as one conversion
     * 2. When we track each visible product as conversion
     *
     * @return string
     */
    private function getConversionPixel(): string
    {
        try {
            if (self::INTEGRATION_TYPE_SKU === $this->getIntegrationType()) {
                return $this->createProductConversionPixels();
            }
            return $this->createOrderConversionPixel();
        } catch (NoSuchEntityException $e) {
            $this->_logger->error('Error happened while creating Everflow conversion pixel.');
            $this->_logger->error($e->getMessage());
            return '';
        }
    }

    /**
     * EF.conversion({
     *   aid: 2, - Advertiser ID
     *   amount: 0, // Order subtotal (cost of products only)
     *   coupon_code: '', // Coupon code entered at checkout
     *   order_id: '',
     *   email: '',
     *   adv1: '', // Please put order skus here (comma-separated)
     *   adv2: '', // Please put product IDs here (comma-separated)
     *   adv3: '',
     *   adv4: '',
     *   adv5: ''
     * })
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createOrderConversionPixel(): string
    {
        $order = $this->orderProvider->getLastOrder();

        $idList = [];
        $skuList = [];
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $idList[] = $item->getProductId();
            $skuList[] = $item->getSku();
        }
        $ids = implode(',', $idList);
        $skus = implode(',', $skuList);

        $jsPixel = $this->getConfigureDomainJs($this->getConversionTrackingDomain()) . PHP_EOL;

        $jsPixel .= "EF.conversion({
            aid: {$this->getAdvertiserId()},
            amount: {$order->getSubtotal()},
            coupon_code: '{$order->getCouponCode()}',
            order_id: '{$order->getIncrementId()}',
            email: '{$order->getCustomerEmail()}',
            adv1: '$skus',
            adv2: '$ids',
            adv3: '',
            adv4: '',
            adv5: ''
        })";

        return $this->jsRenderer->wrapJsCode($jsPixel);
    }

    /**
     * EF.conversion({
     *   aid: 2, - Advertiser ID
     *   amount: 0, // Row price
     *   coupon_code: '', // Coupon code entered at checkout
     *   order_id: '',
     *   email: '',
     *   adv1: '', // Product sku
     *   adv2: '', // Product ID
     *   adv3: '',
     *   adv4: '',
     *   adv5: ''
     * })
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createProductConversionPixels(): string
    {
        $order = $this->orderProvider->getLastOrder();

        $pixelsHtml = $this->jsRenderer->wrapJsCode(
            $this->getConfigureDomainJs($this->getConversionTrackingDomain()) . PHP_EOL
        );

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $pixelsHtml .= $this->jsRenderer->wrapJsCode(
                "EF.conversion({
                    aid: {$this->getAdvertiserId()},
                    amount: {$item->getRowTotal()},
                    coupon_code: '{$order->getCouponCode()}',
                    order_id: '{$order->getIncrementId()}',
                    email: '{$order->getCustomerEmail()}',
                    adv1: '{$item->getSku()}',
                    adv2: '{$item->getProductId()}',
                    adv3: '',
                    adv4: '',
                    adv5: ''
                })"
            );
        }

        return $pixelsHtml;
    }
}
