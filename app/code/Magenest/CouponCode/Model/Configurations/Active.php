<?php
namespace Magenest\CouponCode\Model\Configurations;

use Magento\SalesRule\Model\Data\Rule;

/**
 * Class Active
 * @package Magenest\CouponCode\Model\Configurations
 */
class Active extends AbstractFields
{
    const CODE = "is_active";

    /**
     * {@inheritdoc}
     */
    public function apply($rules)
    {
        if ($this->getConfigurationFieldByCode(self::CODE)) {
            $rules->addFieldToFilter(Rule::KEY_IS_ACTIVE, $this->getConfigurationFieldByCode(self::CODE));
        }
        return $rules;
    }
}
