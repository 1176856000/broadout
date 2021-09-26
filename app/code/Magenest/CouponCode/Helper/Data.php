<?php
namespace Magenest\CouponCode\Helper;

use Magenest\CouponCode\Model\Configurations\Active;
use Magenest\CouponCode\Model\Configurations\CustomerGroup;
use Magenest\CouponCode\Model\Configurations\FromDate;
use Magenest\CouponCode\Model\Configurations\ToDate;
use Magenest\CouponCode\Model\Configurations\UsesPerCoupon;
use Magenest\CouponCode\Model\Configurations\UsesPerCustomer;
use Magenest\CouponCode\Model\Configurations\Website;
use Magento\Store\Model\ScopeInterface;
/**
 * Class DataConfig
 * @package Magenest\CouponCode\Helper
 *
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const COMMUNITY = 'Community';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetadata;

    /**
     * @var string
     */
    const XML_PATH_WEBSITE = 'magenest/couponcode/website_id';
    const XML_PATH_CUSTOMER_GROUP = 'magenest/couponcode/customer_group';
    const XML_PATH_ACTIVE = 'magenest/couponcode/is_active';
    const XML_PATH_FROM = 'magenest/couponcode/from_date';
    const XML_PATH_TO = 'magenest/couponcode/to_date';
    const XML_PATH_USES_PER_COUPON = 'magenest/couponcode/usage_limit';
    const XML_PATH_USES_PER_CUSTOMER = 'magenest/couponcode/usage_per_customer';
    const XML_PATH_ENABLE = 'magenest/couponlisting/enable';
    const XML_PATH_SHOW_FIELDS = 'magenest/couponlisting/showField';

    /**
     * @var array
     */
    protected $_configurations =[
        Active::CODE => self::XML_PATH_ACTIVE,
        Website::CODE => self::XML_PATH_WEBSITE,
        CustomerGroup::CODE => self::XML_PATH_CUSTOMER_GROUP,
        FromDate::CODE => self::XML_PATH_FROM,
        ToDate::CODE => self::XML_PATH_TO,
        UsesPerCoupon::CODE => self::XML_PATH_USES_PER_COUPON,
        UsesPerCustomer::CODE => self::XML_PATH_USES_PER_CUSTOMER
    ];

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_productMetadata = $productMetadata;

        parent::__construct($context);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentWebsiteId()
    {
        return (int) $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return bool
     */
    public function checkCommunityEdition()
    {
        return $this->_productMetadata->getEdition() == self::COMMUNITY;
    }

    public function getMagentoVersion(){
        return $this->_productMetadata->getVersion();
    }
    /**
     * @param $code
     * @return bool
     */
    public function getConfigurationFieldByCode($code){
        return (boolean) $this->scopeConfig->getValue($this->_configurations[$code],ScopeInterface::SCOPE_STORE);
    }

    public function getEnableCouponListingConfiguration(){
	    return (boolean) $this->scopeConfig->getValue(self::XML_PATH_ENABLE,ScopeInterface::SCOPE_STORE);
    }

	public function getFieldDisplayListingConfiguration(){
		return $this->scopeConfig->getValue(self::XML_PATH_SHOW_FIELDS,ScopeInterface::SCOPE_STORE);
	}
}
