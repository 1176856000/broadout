<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model;

/**
 * @method $this setTypes(ResourceModel\Type\Collection $types)
 * @since 1.0.0
 */
class Affiliate extends Affiliate\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Plumrocket_Affiliate';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'praffiliate';

    /**
     * @var \Plumrocket\Affiliate\Model\AffiliateManager
     */
    protected $affiliateManager;

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
     * @param \Plumrocket\Affiliate\Model\AffiliateManager                 $affiliateManager
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
        \Plumrocket\Affiliate\Model\AffiliateManager $affiliateManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->affiliateManager = $affiliateManager;
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
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Plumrocket\Affiliate\Model\ResourceModel\Affiliate');
    }

    /**
     * is affiliate enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * get typed model
     * @param  int $typeId
     * @return \Plumrocket\Affiliate\Model\Affiliate\AbstractModel|\Plumrocket\Affiliate\Api\Data\AffiliateProgramInterface
     */
    public function getTypedModel($typeId = null)
    {
        return $this->affiliateManager->createAffiliate($this, $typeId);
    }
}
