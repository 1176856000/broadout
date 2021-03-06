<?php
namespace Magenest\Popup\Model\ResourceModel\Log;

/**
 * Class Collection
 * @package Magenest\Popup\Model\ResourceModel\Log
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\Popup\Model\Log','Magenest\Popup\Model\ResourceModel\Log');
    }
}