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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Helper;

use Plumrocket\Datagenerator\Model\Template\Condition\Product as ConditionProduct;
use Plumrocket\Datagenerator\Model\Template\Condition\Combine as ConditionCombine;

class Data extends Main
{
    /**
     * Default conditions
     */
    const DEFAULT_CONDITION = [
        'type'               => ConditionCombine::class,
        'attribute'          => null,
        'operator'           => null,
        'value'              => '1',
        'is_value_processed' => null,
        'aggregator'         => 'all',
        'conditions'         =>
            [
                [
                    'type'               => ConditionProduct::class,
                    'attribute'          => 'status',
                    'operator'           => '==',
                    'value'              => '1',
                    'is_value_processed' => false,
                ],
                [
                    'type'               => ConditionProduct::class,
                    'attribute'          => 'visibility',
                    'operator'           => '!=',
                    'value'              => '1',
                    'is_value_processed' => false,
                ],
                [
                    'type'               => ConditionProduct::class,
                    'attribute'          => 'quantity_and_stock_status',
                    'operator'           => '==',
                    'value'              => '1',
                    'is_value_processed' => false,
                ],
            ],
    ];

    /**
     * Needed for Plumrocket Base and for function "getConfigPath"
     * @var string
     */
    protected $_configSectionId = 'prdatagenerator';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Config\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected $productAttributeCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection
     */
    protected $categoryAttributeCollection;

    /**
     * Config section id
     * @var string
     */
    public static $configSectionId = 'prdatagenerator';

    /**
     * Router name
     * @var string
     */
    public static $routeName = 'prdatagenerator';

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface                          $objectManager
     * @param \Magento\Framework\App\Helper\Context                              $context
     * @param \Magento\Framework\App\ResourceConnection                          $resourceConnection
     * @param \Magento\Config\Model\Config                                       $config
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection  $productAttributeCollection
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection $categoryAttributeCollection
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Config\Model\Config $config,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection,
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection $categoryAttributeCollection
    ) {
        parent::__construct($objectManager, $context);
        $this->resourceConnection           = $resourceConnection;
        $this->config                       = $config;
        $this->productAttributeCollection   = $productAttributeCollection;
        $this->categoryAttributeCollection  = $categoryAttributeCollection;
    }

    /**
     * Is module enabled
     * @param  int $store
     * @return boolean
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId.'/general/enabled');
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function disableExtension()
    {
        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [$connection->quoteInto('path = ?', $this->_configSectionId.'/general/enabled')]
        );

        $this->config->setDataByPath($this->_configSectionId.'/general/enabled', 0);
        $this->config->save();

        return $this;
    }

    /**
     * Retrieve attributes
     * @return array
     */
    public function getAttributes()
    {
        $keys = [
            'product' => [],
            'category' => [],
            'site' => [],
            'child' => []
        ];

        // {site.
        $attributes = ['now', 'name', 'phone', 'address', 'url'];
        foreach ($attributes as $attribute) {
            $keys['site'][] = [
                'label' => $attribute,
                'value' => '{site.' . $attribute . '}'
            ];
        }

        // {product.
        $attributes = $this->productAttributeCollection->getItems();
        foreach ($attributes as $attribute) {
            $keys['product'][] = [
                'label' => $attribute->getAttributeCode(),
                'value' => '{product.' . $attribute->getAttributeCode() . '}'
            ];

            $keys['child'][] = [
                'label' => $attribute->getAttributeCode(),
                'value' => '{child.' . $attribute->getAttributeCode() . '}'
            ];
        }

        $extAttributes = ['url', 'image_url', 'small_image_url', 'thumbnail_url', 'sold',
            'special_price', 'price_with_tax', 'price_without_tax', 'qty'];

        foreach ($extAttributes as $attribute) {
            $keys['product'][] = [
                'label' => $attribute,
                'value' => '{product.' . $attribute . '}'
            ];

            $keys['child'][] = [
                'label' => $attribute,
                'value' => '{child.' . $attribute . '}'
            ];
        }

        $childAttributes = ['child_items', 'child'];
        foreach ($childAttributes as $attribute) {
            $keys['product'][] = [
                'label' => $attribute,
                'value' => '{product.' . $attribute . '}{/product.' . $attribute . '}'
            ];
        }

        // {category.
        $attributes = $this->categoryAttributeCollection->getItems();
        foreach ($attributes as $attribute) {
            $keys['category'][] = [
                'label' => $attribute->getAttributeCode(),
                'value' => '{category.' . $attribute->getAttributeCode() . '}'
            ];
        }

        $extAttributes = ['url', 'image_url', 'thumbnail_url', 'breadcrumb_path'];
        foreach ($extAttributes as $attribute) {
            $keys['category'][] = [
                'label' => $attribute,
                'value' => '{category.' . $attribute . '}'
            ];
        }

        return $keys;
    }
}
