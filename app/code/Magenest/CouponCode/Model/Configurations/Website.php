<?php
namespace Magenest\CouponCode\Model\Configurations;

use Magento\SalesRule\Model\Data\Rule;

/**
 * Class Website
 * @package Magenest\CouponCode\Model\Configurations
 */
class Website extends AbstractFields
{
    const CODE = "website_id";

	/**
	 * {@inheritdoc}
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
    public function apply($rules)
    {
        $currentWebsite = $this->getCurrentWebsiteId();
        if ($this->getConfigurationFieldByCode(self::CODE)) {
            $rules->addFieldToFilter(Rule::KEY_WEBSITES, $currentWebsite);
        }

        return $rules;
    }
}
