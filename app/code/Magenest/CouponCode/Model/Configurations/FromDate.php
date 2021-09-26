<?php
namespace Magenest\CouponCode\Model\Configurations;

/**
 * Class FromDate
 * @package Magenest\CouponCode\Model\Configurations
 */
class FromDate extends AbstractFields
{
	const CODE = "from_date";

	/**
	 * {@inheritdoc}
	 */
	public function apply($rules)
	{
		$currentDate = $this->getCurrentDayFromCE();
		if ($currentDate != '' && $this->getConfigurationFieldByCode(self::CODE)) {
			$rules->addFieldToFilter(
				['from_date', 'from_date'],
				[
					['lteq' => $currentDate],
					['null' => true]
				]
			);
		}
		return $rules;
	}
}
