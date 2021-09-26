<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Model\Template\Condition;

use Plumrocket\Datagenerator\Model\Template\Condition\Combine as ConditionCombine;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Plumrocket\Datagenerator\Model\Template\Condition\Product
     */
    protected $_conditionProduct;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param Product $conditionProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        Product $conditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(ConditionCombine::class);

        $this->_conditionProduct = $conditionProduct;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_conditionProduct->loadAttributeOptions()->getAttributeOption();
        $product = [];
        foreach ($productAttributes as $code => $label) {
            $product[] = ['value'=>'Plumrocket\Datagenerator\Model\Template\Condition\Product|'.$code, 'label'=>$label];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => ConditionCombine::class,
                'label'=>__('Conditions combination')
            ],
            ['label'=>__('Current Product Page'), 'value'=>$product],
        ]);

        return $conditions;
    }
}
