<?php
namespace Magenest\Popup\Ui\Component\Listing\Column\Log;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Data
 * @package Magenest\Popup\Ui\Component\Listing\Column\Log
 */
class Data extends \Magento\Ui\Component\Listing\Columns\Column {

    /**
     * @var
     */
    protected $options;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;


    /**
     * Data constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        array $components = [],
        array $data = []
    ){
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->json = $json;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $content = $this->json->unserialize($item['content'] ?? 'null');
                if(is_array($content)){
                    $result = '';
                    $count = 0;
                    foreach ($content as $raw){
                        if($count == 0){
                            $count++;
                            continue;
                        }
                        if(isset($raw['name'])){
                            $result .= $raw['name'].": ".$raw['value']."| ";
                        }
                    }
                    $item['content'] = $result != '' ? $result : $content;
                }
            }
        }
        return $dataSource;
    }
}