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

namespace Plumrocket\Datagenerator\Model;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Datagenerator\Model\Feed\Cleaner as FeedCleaner;
use Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface;
use Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface as TagModifierInterface;
use Plumrocket\Datagenerator\Model\Feed\Tag\Parser as TagParser;
use Plumrocket\Datagenerator\Model\Feed\Tag\RendererInterface as TagRendererInterface;
use Plumrocket\Datagenerator\Model\Feed\TagFactory;
use Plumrocket\Datagenerator\Model\Source\ShareASaleCategory;
use Plumrocket\Datagenerator\Model\Template\Space;
use Psr\Log\LoggerInterface;

/**
 * @method Template getTemplate()
 */
class Render extends DataObject
{
    /**
     * @var \Magento\Framework\Config\CacheInterface
     */
    private $_cache;

    /**
     * @var \Plumrocket\Datagenerator\Helper\Data
     */
    private $_dataHelper;

    /**
     * extension of file
     * @var string
     */
    private $_ext;

    /**
     * filters (rules) status
     * @var string
     */
    private $enabledCondition;

    /**
     * memory limit
     * @var string
     */
    private $_memoryLimit = '1024M';

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $_categoryFactory;

    /**
     * category collection
     * @var array
     */
    private $_categories;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private $_productCollection;

    /**
     * Enabled children render
     * @var boolean
     */
    private $_enabledChildsRender = false;

    /**
     * Enable product category render
     * @var boolean
     */
    private $_enabledProductCategoryRender = false;

    /**
     * Enable site render. It s true when in code item are site tags
     * @var boolean
     */
    private $_enabledSiteRender = false;

    /**
     * Enabled sold qty
     * @var boolean
     */
    private $_enabledSoldQty = false;

    /**
     * Enabled product qty
     * @var boolean
     */
    private $_enabledProductQty = false;

    /**
     * Enabled flat products
     * @var boolean
     */
    private $_enabledFlatProducts = false;

    /**
     * Enabled product stock status
     * @var bool
     */
    private $_enabledProductStockStatus = false;

    /**
     * @var bool
     */
    private $isEnabledProductMediaGallery = false;

    /**
     * @var array
     */
    private $_fields;

    /**
     * Tags
     * @var \Plumrocket\Datagenerator\Model\Feed\TagInterface[]
     */
    private $_tags;

    /**
     * Date time
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_dateTime;

    /**
     * Product count
     * @var integer
     */
    private $_productsCount = 0;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var \Magento\Store\Model\Information
     */
    private $_storeInformation;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    private $_reportsProductCollection;

    /**
     * @var \Magento\Catalog\ResourceModel\Product\Attribute\Collection
     */
    private $_productAttributeCollection;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $_productFactory;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    private $_stockHelper;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $_configurableType;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    private $_groupedType;

    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    private $_bundleType;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $_stockState;

    /**
     * @var \Plumrocket\Datagenerator\Model\Template\Space
     */
    private $spaceModel;

    /**
     * @var array
     */
    private $shareASale = [
        'commission' => [
                'products' => [],
                'enabled'  => false,
        ],
        'subcategory' => [
                'products' => [],
                'enabled'  => false,
        ],
    ];

    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface
     */
    private $productFieldResolver;

    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $_logger;

    /**
     * @var TagParser
     */
    private $tagParser;

    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\TagFactory
     */
    private $tagFactory;

    /**
     * @var TagModifierInterface
     */
    private $tagModifier;

    /**
     * @var FeedCleaner
     */
    private $feedTemplateCleaner;

    /**
     * @var TagRendererInterface
     */
    private $tagRenderer;

    private $_selectAttributes = [];

    private $_soldProducts = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @param \Magento\Framework\Config\CacheInterface                             $cache
     * @param \Plumrocket\Datagenerator\Helper\Data                                $dataHelper
     * @param \Magento\Catalog\Model\CategoryFactory                               $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection              $productCollection
     * @param \Magento\Catalog\Model\ProductFactory                                $productFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                          $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManager
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable         $configurableType
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped                   $groupedType
     * @param \Magento\Bundle\Model\Product\Type                                   $bundleType
     * @param \Magento\CatalogInventory\Api\StockStateInterface                    $stockState
     * @param \Magento\CatalogInventory\Helper\Stock                               $stockHelper
     * @param \Magento\Store\Model\Information                                     $storeInformation
     * @param \Magento\Catalog\Helper\ImageFactory                                 $imageHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection    $productAttributeCollection
     * @param \Magento\Reports\Model\ResourceModel\Product\Sold\Collection         $reportsProductCollection
     * @param \Plumrocket\Datagenerator\Model\Template\Space                       $spaceModel
     * @param \Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface $productFieldResolver
     * @param \Magento\Framework\Registry                                          $registry
     * @param \Psr\Log\LoggerInterface                                             $logger
     * @param \Plumrocket\Datagenerator\Model\Feed\Tag\Parser                      $tagParser
     * @param \Plumrocket\Datagenerator\Model\Feed\TagFactory                      $tagFactory
     * @param \Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface           $tagModifier
     * @param \Plumrocket\Datagenerator\Model\Feed\Cleaner                         $feedTemplateCleaner
     * @param \Plumrocket\Datagenerator\Model\Feed\Tag\RendererInterface           $tagRenderer
     * @param array                                                                $data
     */
    public function __construct(
        \Magento\Framework\Config\CacheInterface $cache,
        \Plumrocket\Datagenerator\Helper\Data $dataHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        Configurable $configurableType,
        Grouped $groupedType,
        Type $bundleType,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        Information $storeInformation,
        \Magento\Catalog\Helper\ImageFactory $imageHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributeCollection,
        \Magento\Reports\Model\ResourceModel\Product\Sold\Collection $reportsProductCollection,
        Space $spaceModel,
        FieldResolverInterface $productFieldResolver,
        Registry $registry,
        LoggerInterface $logger,
        TagParser $tagParser,
        TagFactory $tagFactory,
        TagModifierInterface $tagModifier,
        FeedCleaner $feedTemplateCleaner,
        TagRendererInterface $tagRenderer,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_cache = $cache;
        $this->_dataHelper = $dataHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollection = $productCollection;
        $this->_productFactory = $productFactory;
        $this->_dateTime = $dateTime;
        $this->_storeManager = $storeManager;
        $this->_configurableType = $configurableType;
        $this->_groupedType = $groupedType;
        $this->_bundleType = $bundleType;
        $this->_stockState = $stockState;
        $this->_stockHelper = $stockHelper;
        $this->_storeInformation = $storeInformation;
        $this->_productAttributeCollection = $productAttributeCollection;
        $this->_reportsProductCollection = $reportsProductCollection;
        $this->spaceModel = $spaceModel;
        $this->productFieldResolver = $productFieldResolver;
        $this->_registry = $registry;
        $this->_logger = $logger;
        $this->tagParser = $tagParser;
        $this->tagFactory = $tagFactory;
        $this->tagModifier = $tagModifier;
        $this->feedTemplateCleaner = $feedTemplateCleaner;
        $this->tagRenderer = $tagRenderer;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Is data preparing at this moment
     * @return boolean
     */
    public function isRunning(): bool
    {
        $time = $this->_cache->load('datafeed_run_' . $this->getTemplate()->getId());
        $maxRunTime = (int)$this->getTemplate()->getData('cache_time');
        if (!$maxRunTime || $maxRunTime > 3600) {
            $maxRunTime = 3600;
        }
        return ($time > time() - $maxRunTime);
    }

    /**
     * Get content
     * @param Template|null $template
     * @return string
     */
    public function getText($template = null): string
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return '';
        }

        if (null !== $template) {
            $this->setTemplate($template);
        }

        $template = $this->getTemplate();

        $this->_ext = $template->getExt();

        $text = $this->getTextCache();

        if (! $text) {
            try {
                ini_set('memory_limit', $this->_memoryLimit);
            } catch (\Exception $e) {
                $this->_logger->debug("Can't change memory limit", ['exception' => $e]);
            }

            $this->_startRun();
            $data = $template->getData();

            $this->_loadCategories();
            $this->_checkEnabledOptions($data);
            $this->_collectionTags($template, $template->getReplaceFrom(), $template->getReplaceTo());
            $this->isConditionEnabled();

            $text = $this->_renderHeader($template->getHeaderTemplate());
            $text .= $this->_renderItems(
                $template->getItemTemplate(),
                $template->getMaxItemsCount(),
                $template->getType()
            );
            $text .= $this->_renderFooter($template->getFooterTemplate());

            $text = $this->feedTemplateCleaner->cleanTemplate($text, $template->getExt());
            $this->_cache->save($text, $this->_getTextCacheKey(), ['datafeed'], (int)$template->getData('cache_time'));

            $this->_endRun();
        }
        return $text;
    }

    /**
     * Get text cache
     * @return string|bool
     */
    public function getTextCache()
    {
        return $this->_cache->load(
            $this->_getTextCacheKey()
        );
    }

    /**
     * Get cache key
     * @return string
     */
    protected function _getTextCacheKey(): string
    {
        return 'datafeed_' . $this->getTemplate()->getId();
    }

    /**
     * Start run
     * @return $this
     */
    protected function _startRun(): self
    {
        $this->_cache->save((string)time(), 'datafeed_run_' . $this->getTemplate()->getId(), [], 86400);
        return $this;
    }

    /**
     * Load categories
     * @return $this
     */
    protected function _loadCategories(): self
    {
        $storeId = $this->getTemplate()->getStoreId();
        $cats = $this->_categoryFactory->create()
            ->getCollection()
            ->addUrlRewriteToResult()
            ->setStoreId($storeId)
            ->addFieldToFilter('is_active', 1)
            ->addAttributeToSelect('*')
            ->load();

        // Any modules might change isActive result in realtime
        foreach ($cats as $cat) {
            $this->_categories[ $cat->getId() ] = $cat;
        }

        return $this;
    }

    /**
     * Checking enabled options
     *
     * @param  array $data
     * @return $this
     */
    protected function _checkEnabledOptions($data): self
    {
        $codeItem = $data['code_item'];
        $this->_enabledChildsRender = strpos($codeItem, '{product.child_items}') !== false;
        $this->_enabledProductCategoryRender = strpos($codeItem, '{category.') !== false;
        $this->_enabledSiteRender = strpos($codeItem, '{site.') !== false;
        $this->_enabledSoldQty = strpos($codeItem, '.sold}') !== false;
        $this->_enabledProductQty = strpos($codeItem, '.qty}') !== false;
        $this->_enabledProductStockStatus = strpos($codeItem, '.stock_status}') !== false;
        // All feeds have media_gallery, but it can be rendered only with "|repeat" modifier
        $this->isEnabledProductMediaGallery = strpos($codeItem, '{product.media_gallery|repeat:') !== false;

        $this->shareASale['commission']['enabled'] =
            strpos($codeItem, '.custom_commission}') !== false
            || strpos($codeItem, '.custom_commissions_flat_rate}') !== false;
        $this->shareASale['subcategory']['enabled'] = strpos($codeItem, '.share_a_sale_subcategory}') !== false;

        // fix for flat products
        $this->_enabledFlatProducts = $this->_productCollection->isEnabledFlat();
        if ($this->_enabledFlatProducts) {
            $this->_fields = [];
            foreach (['image', 'small_image', 'thumbnail'] as $field) {
                if (! in_array($field, $this->_fields, true)) {
                    $this->_fields[] = $field;
                }
            }
        }

        return $this;
    }

    /**
     * Collection tags
     *
     * Some feeds use $replaceFrom and $replaceTo to replace same character in all values
     *
     * @param \Plumrocket\Datagenerator\Model\Template $template
     * @param mixed                                    $replaceFrom
     * @param mixed                                    $replaceTo
     * @return $this
     */
    protected function _collectionTags(Template $template, $replaceFrom, $replaceTo): self
    {
        $tagsData = array_merge(
            $this->tagParser->parse($template->getHeaderTemplate()),
            $this->tagParser->parse($template->getItemTemplate()),
            $this->tagParser->parse($template->getFooterTemplate())
        );

        $globalReplace = false;
        if ($replaceFrom && is_scalar($replaceFrom) && is_scalar($replaceTo)) {
            $globalReplace = "$replaceFrom:$replaceTo";
        }

        $tags = [];
        foreach ($tagsData as $tagData) {
            $tag = $this->tagFactory->create($tagData);
            if ($globalReplace) {
                $tag->setModifier('replace', $globalReplace);
            }
            $tags[] = $tag;
        }

        $this->_tags = [];
        $childrenTags = ['product' => ['child_items', 'child']];
        foreach ($tags as $tag) {
            if (array_key_exists('repeat', $tag->getModifiers())) {
                $childrenTags[$tag->getModifiers()['repeat']] = '*';
            }

            if ($this->isChildTag($tag, $childrenTags)) {
                continue;
            }

            $this->_tags[] = $tag;
        }

        return $this;
    }

    /**
     * Render header
     *
     * @param  string $feedHeaderTemplate
     * @return string
     */
    private function _renderHeader($feedHeaderTemplate): string
    {
        return $this->renderSiteTags($feedHeaderTemplate);
    }

    /**
     * Render Site tags in template
     *
     * @param string $feedTemplatePart
     * @return string
     */
    private function renderSiteTags(string $feedTemplatePart): string
    {
        $store = $this->getStore();
        $data = [
            'now'       => $this->_dateTime->date('Y-m-d H:i:s'),
            'name'      => $store->getConfig(Information::XML_PATH_STORE_INFO_NAME),
            'phone'     => $store->getConfig(Information::XML_PATH_STORE_INFO_PHONE),
            'address'   => $this->_storeInformation->getFormattedAddress($store),
            'url'       => $store->getBaseUrl(),
            'count'     => $this->_productsCount,
        ];

        foreach ($this->_tags as $tag) {
            if ($tag->getEntityType() !== 'site') {
                continue;
            }

            $val = $data[$tag->getPropertyName()] ?? '';
            $val = $this->tagModifier->modify($tag, $val);

            $feedTemplatePart = $this->tagRenderer->render($tag, $val, $feedTemplatePart, $this->_ext);
        }

        return $feedTemplatePart;
    }

    /**
     * Render items
     *
     * @param string $itemTemplate
     * @param int    $count
     * @param int    $type
     * @return string
     */
    private function _renderItems(string $itemTemplate, int $count, int $type): string
    {
        $result = '';
        $iter = 0;

        if (Template::ENTITY_FEED_TYPE_PRODUCT === $type) {
            // load sold info if {xxx.sold} exists
            if ($this->_enabledSoldQty) {
                $this->_loadSoldQty();
            }

            // get select attributes
            $attributes = $this->_productAttributeCollection->getItems();
            foreach ($attributes as $attribute) {
                if (($attribute->getData('frontend_input') === 'select')
                    && $attribute->usesSource()
                ) {
                    $options = $attribute->getSource()->getAllOptions(false);
                    foreach ($options as $item) {
                        if (!is_array($item['value'])) {
                            $this->_selectAttributes[$attribute->getData('attribute_code')][(string)$item['value']]
                                = $item['label'];
                        }
                    }
                }
            }

            $lastProductId = 999999;

            do {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this->productCollectionFactory->create();

                // get products
                $collection
                    ->addFieldToFilter('entity_id', ['lt' => $lastProductId])
                    ->addUrlRewrite()
                    ->addAttributeToSelect('*')
                    ->addWebsiteFilter();

                $collection->setFlag('has_stock_status_filter', true); //add load out_stock products

                // fix for flat products
                if ($this->_enabledFlatProducts) {
                    foreach ($this->_fields as $field) {
                        $collection->joinAttribute($field, 'catalog_product/image', 'entity_id', null, 'left');
                    }
                }

                $collection->getSelect()
                    ->limit(500)
                    ->order('e.entity_id DESC');

                $this->_stockHelper->addInStockFilterToCollection($collection);

                if ($this->isEnabledProductMediaGallery) {
                    $collection->addMediaGalleryData();
                }

                foreach ($collection as $product) {
                    $lastProductId = $product->getId();
                    if ($this->enabledCondition) {
                        $space = $this->spaceModel->getSpace($product);
                        $res = $this->getTemplate()->validate($space)
                                ? $this->_renderProduct($product, $itemTemplate)
                                : null;
                    } else {
                        $res = $this->_renderProduct($product, $itemTemplate);
                    }
                    if ($res) {
                        $iter++;
                        if (($count > 0) && ($iter > $count)) {
                            break;
                        }
                        $this->_productsCount++;

                        $result .= "\n" . $res;
                    }
                }

            } while (($collection->count() > 0) && (($count === 0) || ($iter < $count)));

        } elseif (Template::ENTITY_FEED_TYPE_CATEGORY === $type) {
            foreach ($this->_categories as $cat) {
                $res = $this->_renderCategory($cat, $itemTemplate);
                if ($res) {
                    $iter++;
                    if (($count > 0) && ($iter > $count)) {
                        break;
                    }
                    $result .= "\n" . $res;
                }
            }
        }
        return $result;
    }

    /**
     * init condition settings
     * @return boolean
     */
    protected function isConditionEnabled()
    {
        $conditions = $this->getTemplate()->getConditions()->asArray();
        if ($this->enabledCondition === null) {
            $this->enabledCondition = isset($conditions['conditions'][0]) && !empty($conditions['conditions'][0]);
        }
        return $this->enabledCondition;
    }

    /**
     * Load sold qty
     * @return $this
     */
    protected function _loadSoldQty()
    {
        // get sold products
        $products = $this->_reportsProductCollection
            ->addOrderedQty();

        foreach ($products as $prod) {
            $this->_soldProducts[$prod->getId()] = $prod->getOrderedQty();
        }

        return $this;
    }

    /**
     * Render product
     *
     * @param  \Magento\Catalog\Model\Product $prod product
     * @param  string                         $feedItemTemplate
     * @return string
     */
    protected function _renderProduct($prod, $feedItemTemplate): string
    {
        $prodCats = $prod->getCategoryIds();

        $rootCategoryId = $this->getStore()->getRootCategoryId();

        $cat = null;
        $this->_registry->unregister('current_category');

        foreach ($prodCats as $catId) {
            if (isset($this->_categories[ $catId ]) && $catId != $rootCategoryId) {
                $cat = $this->_categories[ $catId ];

                // Set current category for check product status.
                $this->_registry->register('current_category', $cat);
                break;
            }
        }

        $children = null;
        if ($this->_enabledChildsRender) {
            $children = $this->_loadChildProducts($prod);
        }
        $feedItemTemplate = $this->_renderProductEntity($prod, $feedItemTemplate, 'product', $children);
        if ($this->_enabledChildsRender) {
            $feedItemTemplate = $this->_renderChildren($prod, $children, $feedItemTemplate);
        }

        // Render site tags in code item
        if ($this->_enabledSiteRender) {
            $feedItemTemplate = $this->renderSiteTags($feedItemTemplate);
        }

        // Render category
        if ($this->_enabledProductCategoryRender) {
            $feedItemTemplate = $this->_renderCategory($cat, $feedItemTemplate, $prod);
        }

        return $feedItemTemplate;
    }

    /**
     * Load child products
     * @param \Magento\Catalog\Model\Product $product
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _loadChildProducts($product)
    {
        $productId = $product->getId();
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $ids = $this->_configurableType->getChildrenIds($productId);
        } elseif ($product->getTypeId() === Grouped::TYPE_CODE) {
            $ids = $this->_groupedType->getChildrenIds($productId);
        } elseif ($product->getTypeId() === Type::TYPE_CODE) {
            $ids = $this->_bundleType->getChildrenIds($productId);
        } else {
            $ids = [];
        }

        if (count($ids)) {
            return $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => [$ids]])
                ->load();
        }

        return [];
    }

    /**
     * Render product entity
     *
     * @param  \Magento\Catalog\Model\Product                               $prod
     * @param  string                                                       $feedItemTemplate
     * @param  string                                                       $type
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection|null $children
     * @return string
     */
    protected function _renderProductEntity($prod, $feedItemTemplate, $type, $children = null): string
    {
        $data = $this->_getProductData($prod, $this->_registry->registry('current_category'), $children);
        $store = $this->getStore();

        foreach ($this->_tags as $tag) {
            if ($type !== $tag->getEntityType()) {
                continue;
            }

            try {
                $val = $this->productFieldResolver->resolve($tag, $prod, $store, $data);
            } catch (NotFoundException $e) {
                $val = $data[$tag->getPropertyName()] ?? '';
            }

            $val = $this->tagModifier->modify($tag, $val, '', $prod);
            $feedItemTemplate = $this->tagRenderer->render($tag, $val, $feedItemTemplate, $this->_ext);
        }

        return $feedItemTemplate;
    }

    /**
     * Get product data
     * @param \Magento\Catalog\Model\Product $prod
     * @param \Magento\Catalog\Model\Category $cat
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $children
     * @return array
     */
    protected function _getProductData($prod, $cat, $children): array
    {
        $data = $prod->getData();

        $data = array_merge(
            $data,
            [
                'id'        => $prod->getId(),
                'sold'      => (int) ($this->_soldProducts[$prod->getId()] ?? 0),
                'is_bundle' => $prod->getTypeId() === Type::TYPE_CODE ? 'yes' : 'no',
            ]
        );

        if ($this->_enabledProductQty) {
            $qty = 0;
            if ($prod['type_id'] === Configurable::TYPE_CODE) {
                $associated_products = $this->loadByAttribute($prod, 'sku', $data['sku'])
                    ->getTypeInstance()
                    ->getUsedProducts($prod);
                foreach ($associated_products as $assoc) {
                    $assocProduct = $this->_productFactory->create()->load($assoc->getId());
                    $qty += (int)$this->_stockState->getStockQty($assocProduct->getId());
                }
            } else {
                $qty = (int)$this->_stockState->getStockQty($prod->getId());
            }
            $data['qty'] = $qty;
        }

        if ($this->_enabledProductStockStatus) {
            $data['stock_status'] = $prod->getQuantityAndStockStatus()
                ? 'in stock'
                : 'out of stock';
        }

        //For special ShareASale fields
        if ($this->shareASale['commission']['enabled']) {
            $data = $this->getShareASaleCommission($data, $cat);
        }

        if ($this->shareASale['subcategory']['enabled']) {
            $data = $this->getShareASaleCategory($data, $cat);
        }

        // replace select attributes to their values
        foreach ($data as $code => $val) {
            // for ShareASale
            if ($this->shareASale['commission']['enabled'] && $code === 'custom_commissions_flat_rate') {
                continue;
            }
            if (isset($this->_selectAttributes[$code][$val])) {
                $data[$code] = $this->_selectAttributes[$code][$val];
            }
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $attribute
     * @param                                $value
     * @param string                         $additionalAttributes
     * @return false|mixed
     */
    public function loadByAttribute($product, $attribute, $value, $additionalAttributes = '*')
    {
        $collection = $product->getResourceCollection()
            ->addAttributeToSelect($additionalAttributes)
            ->addAttributeToFilter($attribute, $value)
            ->setPage(1, 1)
            ->setFlag('has_stock_status_filter', true);

        return $collection->count() ? $collection->getFirstItem() : false;
    }

    /**
     * @param $prod
     * @param $products
     * @param $text
     * @return string
     */
    protected function _renderChildren($prod, $products, $text): string
    {
        preg_match_all('/[\s]*\{product\.child_items\}(.*)\{\/product\.child_items\}[\s]*/smU', $text, $sections);

        if (isset($sections[1])) {
            foreach ($sections[1] as $section_id => $section_text) {
                if ($products) {
                    preg_match_all('/[\s]*\{product\.child\}(.*)\{\/product\.child\}[\s]*/smU', $section_text, $blocks);

                    if (isset($blocks[1])) {
                        foreach ($blocks[1] as $block_id => $block_text) {
                            $block_text = rtrim($block_text);
                            $products_text = '';

                            foreach ($products as $pr) {
                                $products_text .= $this->_renderProductEntity($pr, $block_text, 'child');
                            }
                            $section_text = str_replace($blocks[0][$block_id], $products_text, $section_text);
                        }
                    }
                } else {
                    $section_text = '';
                }
                $text = str_replace($sections[0][$section_id], $section_text, $text);
            }
        }

        return $text;
    }

    /**
     * Render category
     *
     * @param \Magento\Catalog\Model\Category $cat
     * @param  string                         $feedItemTemplate
     * @param \Magento\Catalog\Model\Product  $product
     * @return string
     */
    protected function _renderCategory($cat, $feedItemTemplate, $product = null): string
    {
        $data = $this->_getCategoryData($cat, $product);

        if ($cat) {
            $data['url'] = $cat->getUrl();
        }

        foreach ($this->_tags as $tag) {
            if ('category' !== $tag->getEntityType()) {
                continue;
            }

            $val = $data[$tag->getPropertyName()] ?? '';
            $val = $this->tagModifier->modify($tag, $val, '', $cat);
            $feedItemTemplate = $this->tagRenderer->render($tag, $val, $feedItemTemplate, $this->_ext);
        }

        return $feedItemTemplate;
    }

    /**
     * Get category data
     * @param \Magento\Catalog\Model\Category $cat
     * @param \Magento\Catalog\Model\Product|null $product
     * @return array|null
     */
    protected function _getCategoryData($cat, $product = null)
    {
        if (!is_object($cat)) {
            return null;
        }

        $data = $cat->getData();
        $store = $this->getStore();
        $template = $this->getTemplate();

        if ((int) $template->getTemplateId() === $template->getGoogleShoppingId()) {
            $data['name'] = $product && $product->getData('google_product_category')
                ? $product->getData('google_product_category')
                : $cat->getData('google_product_category');
        }

        $data = array_merge(
            $data,
            [
                'id'                => $cat->getId(),
                'breadcrumb_path'   => (string)$this->_getBreadcrumbPath($cat),
                'image_url'         => $cat->getImageUrl(),
                'thumbnail_url'     => $cat->getThumbnailUrl()
            ]
        );

        if (isset($data['url_path'])) {
            $data['url'] = str_replace('?___SID=U', '', $store->getUrl($data['url_path']));
        }

        return $data;
    }

    /**
     * Retrieve breadcrumb path
     * @param  \Magento\Catalog\Model\Category $category
     * @return string
     */
    protected function _getBreadcrumbPath($category)
    {
        $path = [];
        $pathInStore = $category->getPathInStore();

        $pathIds = array_reverse(explode(',', $pathInStore));

        $categories = $category->getParentCategories();

        // add category path breadcrumb
        foreach ($pathIds as $categoryId) {
            if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                $path[] = $categories[$categoryId]->getName();
            }
        }

        return implode(' > ', $path);
    }

    /**
     * Render footer
     *
     * @param string $feedFooterTemplate
     * @return string
     */
    protected function _renderFooter(string $feedFooterTemplate)
    {
        return "\n" . $this->renderSiteTags($feedFooterTemplate);
    }

    /**
     * end run
     * @return $this
     */
    protected function _endRun()
    {
        $this->_cache->remove('datafeed_run_' . $this->getTemplate()->getId());
        return $this;
    }

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface $tag
     * @param array                                             $childrenTags
     * @return bool
     */
    private function isChildTag(Feed\TagInterface $tag, array $childrenTags): bool
    {
        if (array_key_exists($tag->getEntityType(), $childrenTags)) {
            $excludeList = $childrenTags[$tag->getEntityType()];
            if (is_array($excludeList)) {
                return in_array($tag->getPropertyName(), $childrenTags[$tag->getEntityType()], true);
            }

            return true;
        }

        return false;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    private function getStore(): StoreInterface
    {
        return $this->_storeManager->getStore($this->getTemplate()->getStoreId());
    }

    public function getShareASaleFromProduct($data, $type, $field)
    {
        if (isset($this->shareASale[$type]['products'][$data['id']])
            && is_array($this->shareASale[$type]['products'][$data['id']])
        ) {
            return $this->shareASale[$type]['products'][$data['id']];
        }

        return !(!isset($data[$field]) || isset($data[$field])
            && (!$data[$field] || is_object($data[$field]) && !$data[$field]->getArguments())
        );
    }

    protected function getShareASaleCommission($data, $cat)
    {
        if (isset($data['custom_commission']) && $data['custom_commission'] === 'D') {
            $data['custom_commissions_flat_rate'] = '';
            return $data;
        }
        //search in product
        $fromProduct = $this->getShareASaleFromProduct($data, 'commission', 'custom_commissions_flat_rate');
        if ($fromProduct) {
            if (!is_array($fromProduct)) {
                $this->shareASale['commission']['products'][$data['id']] = [
                    'custom_commission'            => (int)$data['custom_commission'],
                    'custom_commissions_flat_rate' => --$data['custom_commissions_flat_rate'],
                ];
            }
            return array_merge($data, $this->shareASale['commission']['products'][$data['id']]);
        }

        //if need search in category
        $this->getShareASaleCommissionByCategory($cat);

        if (empty($this->shareASale['commission'][$cat->getId()])) {
            $data['custom_commission'] = 'D';
            $data['custom_commissions_flat_rate'] = '';
        } else {
            $this->shareASale['commission']['products'][$data['id']] = $this->shareASale['commission'][$cat->getId()];
            $data = array_merge($data, $this->shareASale['commission'][$cat->getId()]);
        }

        return $data;
    }

    protected function getShareASaleCommissionByCategory($category)
    {
        $categoryId = (int)(is_object($category) ? $category->getId() : $category);
        if (!$categoryId) {
            return null;
        }

        if (isset($this->shareASale['commission'][$categoryId])) {
            return $this->shareASale['commission'][$categoryId];
        }

        if (!is_object($category)) {
            if (isset($this->_categories[$categoryId])) {
                $category = $this->_categories[$categoryId];
            } else {
                $category = $this->_categoryFactory->create()->load($categoryId);
                $this->_categories[$categoryId] = $category;
            }
        }

        $shareASale = [];
        if ($category->getCustomCommissionsFlatRate() == 0) {
            $shareASale = $this->getShareASaleCommissionByCategory($category->getParentId());
        } elseif (is_numeric($category->getCustomCommissionsFlatRate())) {
            $shareASale = [
                'custom_commission' => (int)$category->getCustomCommission(),
                'custom_commissions_flat_rate' => is_object($category->getCustomCommissionsFlatRate())
                    ? $category->getCustomCommissionsFlatRate()->getArguments() - 1
                    : (int)$category->getCustomCommissionsFlatRate() - 1,
            ];
        }

        return $this->shareASale['commission'][$categoryId] = $shareASale;
    }

    protected function getShareASaleCategory($data, $cat)
    {
        $fromProduct = $this->getShareASaleFromProduct($data, 'subcategory', 'share_a_sale_subcategory');
        if ($fromProduct) {
            return array_merge(
                $data,
                [
                    'share_a_sale_subcategory' => $data['share_a_sale_subcategory'],
                    'share_a_sale_category' => ShareASaleCategory::getCategoryIdBySubcategory(
                        $data['share_a_sale_subcategory']
                    ),
                ]
            );
        }

        //if need search in category
        $this->getShareASaleSubCategoryByCategory($cat);

        if (empty($this->shareASale['subcategory'][$cat->getId()])) {
            $data['share_a_sale_category'] = '';
            $data['share_a_sale_subcategory'] = '';
        } else {
            $data = array_merge($data, $this->shareASale['subcategory'][$cat->getId()]);
        }

        return $data;
    }

    protected function getShareASaleSubCategoryByCategory($category)
    {
        $categoryId = (int)(is_object($category) ? $category->getId() : $category);
        if (!$categoryId) {
            return null;
        }

        if (isset($this->shareASale['subcategory'][$categoryId])) {
            return $this->shareASale['subcategory'][$categoryId];
        }

        if (!is_object($category)) {
            if (isset($this->_categories[$categoryId])) {
                $category = $this->_categories[$categoryId];
            } else {
                $category = $this->_categoryFactory->create()->load($categoryId);
                $this->_categories[$categoryId] = $category;
            }
        }

        $result = [];
        if ($category->getShareASaleSubcategory() == 0) {
            $result = $this->getShareASaleSubCategoryByCategory($category->getParentId());
        } elseif (is_numeric($category->getShareASaleSubcategory())) {
            $result = [
                'share_a_sale_subcategory' => is_object($category->getShareASaleSubcategory())
                    ? $category->getShareASaleSubcategory()->getArguments()
                    : $category->getShareASaleSubcategory(),
            ];
            if ($result['share_a_sale_subcategory']) {
                $result['share_a_sale_category'] = ShareASaleCategory::getCategoryIdBySubcategory(
                    $result['share_a_sale_subcategory']
                );
            }
        }

        return $this->shareASale['subcategory'][$categoryId] = $result;
    }
}
