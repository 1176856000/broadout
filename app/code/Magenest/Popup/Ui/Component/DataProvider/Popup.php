<?php


namespace Magenest\Popup\Ui\Component\DataProvider;

use Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class Popup
 * @package Magenest\Popup\Ui\Component\DataProvider
 */
class Popup extends AbstractDataProvider
{
    /**
     * @var
     */
    protected $_loadedData;
    /**
     * @var \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    /**
     * Popup constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->_collectionFactory=$collectionFactory;
        $this->collection=$this->_collectionFactory->create();
        $this->_json = $json;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $data = $item->getData();
            $data['background_image'] = $this->_json->unserialize($item->getData('background_image') ?? 'null');
            $this->_loadedData[$item->getId()] = $data;
            unset($this->_loadedData[$item->getId()]['widget_instance']);
        }
        return $this->_loadedData;
    }
}
