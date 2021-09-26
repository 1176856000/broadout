<?php
namespace Magenest\Popup\Model\ResourceModel\Template;

/**
 * Class Collection
 * @package Magenest\Popup\Model\ResourceModel\Template
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
    /**
     * @var string
     */
    protected $_idFieldName = 'template_id';

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\Popup\Model\Template','Magenest\Popup\Model\ResourceModel\Template');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id','template_name');
    }
}