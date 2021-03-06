<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Rule\Condition;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * @param Collection $collection
     * @param ProductModel $product
     * @param int $equals
     */
    public function apply(Collection $collection, ProductModel $product, $equals)
    {
        $attributeCode = $this->getAttribute();

        return $attributeCode == 'category_ids'
            ? $this->addCategoryFilter($collection, $product, $equals)
            : $this->addAttributeFilter($collection, $product, $attributeCode, (bool)$equals);
    }

    /**
     * @param Collection $collection
     * @param ProductModel $product
     * @param string $attributeCode
     * @param bool $equals
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addAttributeFilter(
        Collection $collection,
        ProductModel $product,
        string $attributeCode,
        bool $equals
    ) {
        $result = false;
        $attribute = $this->getAttributeObject();

        if ($attribute->getId()) {
            $attributeValue = $product->getData($attributeCode);

            if ($attributeValue === null) {
                $attributeValue = $this->getAttributeValue($product, $attributeCode) ?: '';
            }

            if ($this->isAttributeMultiselect($product, $attributeCode)) {
                $this->applyMultiselectAttribute($collection, $attributeValue, $attributeCode, $equals);
            } else {
                $collection->addAttributeToFilter(
                    $attributeCode,
                    [$equals ? 'eq' : 'neq' => $attributeValue]
                );
            }

            $result = true;
        }

        return $result;
    }

    /**
     * @param $collection
     * @param $attributeValue
     * @param $attributeCode
     * @param $equals
     */
    private function applyMultiselectAttribute($collection, $attributeValue, $attributeCode, $equals)
    {
        $filter = [];
        foreach (explode(',', $attributeValue) as $val) {
            $filter[] = [
                'attribute' => $attributeCode,
                'finset' => $val
            ];
        }
        if (!empty($filter)) {
            $collection->addAttributeToFilter($filter);
            if (!$equals) {
                $where = $collection->getSelect()->getPart(\Zend_Db_Select::WHERE);
                $fInSetCondition = array_pop($where);
                $fInSetCondition = str_replace('FIND_IN_SET', 'NOT FIND_IN_SET', $fInSetCondition);
                $fInSetCondition = str_replace(
                    \Zend_Db_Select::SQL_OR,
                    \Zend_Db_Select::SQL_AND,
                    $fInSetCondition
                );
                $where[] = $fInSetCondition;
                $collection->getSelect()->setPart(\Zend_Db_Select::WHERE, $where);
            }
        }
    }

    /**
     * @param ProductModel $product
     * @param string $attributeCode
     * @return bool
     */
    private function isAttributeMultiselect(ProductModel $product, $attributeCode)
    {
        return $product->getResource()->getAttribute($attributeCode)->getFrontendInput() == 'multiselect';
    }

    /**
     * @param Collection $collection
     * @param ProductModel $product
     * @param $equals
     *
     * @return bool
     */
    private function addCategoryFilter(Collection $collection, ProductModel $product, $equals)
    {
        $applied = false;
        $attributeValue = $product->getCategoryIds();
        if ($attributeValue) {
            $applied = true;
            //do not use resource model because adding new dependency into this class is not a good option
            $collection->getSelect()->join(
                ['ccproduct' => $this->_productResource->getTable('catalog_category_product')],
                sprintf(
                    'e.entity_id = ccproduct.product_id AND ccproduct.category_id %s(%s)',
                    $equals ? 'IN' : 'NOT IN',
                    implode(',', $attributeValue)
                ),
                []
            );
            $collection->getSelect()->group('e.entity_id');
        }

        return $applied;
    }

    /**
     * @param ProductModel $product
     * @param string $attributeCode
     * @return mixed
     */
    private function getAttributeValue(ProductModel $product, $attributeCode)
    {
        return $product->getResource()->getAttributeRawValue(
            $product->getId(),
            $attributeCode,
            $product->getStoreId()
        );
    }

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            $this->_defaultOperatorInputByType = [
                'string' => ['=='],
                'numeric' => ['=='],
                'date' => ['=='],
                'select' => ['=='],
                'boolean' => ['=='],
                'multiselect' => ['=='],
                'grid' => ['=='],
                'category' => ['=='],
                'sku' => ['=='],
            ];
            $this->_arrayInputTypes[] = 'category';
        }

        return $this->_defaultOperatorInputByType;
    }

    /**
     * @return string
     */
    public function getValueElementHtml()
    {
        return __('same as Current Product ') . $this->getAttributeObject()->getDefaultFrontendLabel();
    }

    /**
     * @return array
     */
    public function getAttributeSelectOptions()
    {
        $opt = [
            [
                'value' => '1',
                'label' => __('same as Current Product ') . $this->getAttributeObject()->getDefaultFrontendLabel()
            ]
        ];

        return $opt;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        return $this->getAttributeSelectOptions();
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        return \Amasty\Mostviewed\Model\Group::FORM_NAME;
    }
}
