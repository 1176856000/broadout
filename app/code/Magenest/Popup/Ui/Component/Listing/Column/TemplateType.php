<?php
namespace Magenest\Popup\Ui\Component\Listing\Column;

/**
 * Class TemplateType
 * @package Magenest\Popup\Ui\Component\Listing\Column
 */
class TemplateType  extends \Magento\Ui\Component\Listing\Columns\Column {
    /**
     * @var \Magenest\Popup\Helper\Data
     */
    protected $_helperData;
    /**
     * @var
     */
    protected $options;

    /**
     * TemplateType constructor.
     * @param \Magenest\Popup\Helper\Data $helperData
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magenest\Popup\Helper\Data $helperData,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        $this->_helperData = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            $templateType = $this->_helperData->getTemplateType();
            foreach ($dataSource['data']['items'] as & $item) {
                $template_type = $item['template_type'];
                if($templateType[$template_type]){
                    $item['template_type'] = $templateType[$template_type]->getText();
                }
            }
        }
        return $dataSource;
    }
}