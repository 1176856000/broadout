<?php
namespace Magenest\CouponCode\Model\Configurations;

/**
 * Class CustomerGroup
 * @package Magenest\CouponCode\Model\Configurations
 */
class CustomerGroup extends AbstractFields
{
    const CODE = "customer_group_id";

    /**
     * @var string
     */
    const NOT_LOGGED_IN = '0';

    /**
     * {@inheritdoc}
     */
    public function apply($rules)
    {
        $customer = $this->getCustomer();
        $customerId = $customer->getId();
        if ($this->getConfigurationFieldByCode(self::CODE)) {
            if ($customerId) {
                $rules->addFieldToFilter('customer_group_id', ['finset' => $customer->getGroupId()]);
            } else {
                $rules->addFieldToFilter('customer_group_id', ['finset' => self::NOT_LOGGED_IN]);
            }
        }
        return $rules;
    }
}
