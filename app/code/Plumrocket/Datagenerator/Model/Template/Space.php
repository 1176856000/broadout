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

namespace Plumrocket\Datagenerator\Model\Template;

class Space extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productInterface;

    /**
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface         $stockRegistryInterface
     * @param \Magento\Catalog\Api\ProductRepositoryInterface              $productInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context                        $context,
        \Magento\Framework\Registry                             $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface    $stockRegistryInterface,
        \Magento\Catalog\Api\ProductRepositoryInterface         $productInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection = null,
        array $data = []
    ) {
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->productInterface       = $productInterface;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    public function getSpace($product)
    {
        if ($product->getId()) {
            switch ($product->getTypeId()) {
                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                    $isInStock = $product->isAvailable();
                    if (null === $isInStock) {
                        $isInStock = $this->productInterface->getById($product->getId())->isAvailable();
                    }
                    break;
                case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                    $associatedProducts = $product->getTypeInstance()->getAssociatedProductIds($product);
                    $isInStock = count($associatedProducts) > 0 && $this->getIsInStockGrouped($product);
                    break;
                default:
                    $isInStock = $this->getIsInStock($product);
                    break;
            }
        }

        $product->setQuantityAndStockStatus((int)$isInStock);
        $this->setData('product', $product);
        return $this;
    }

    protected function getIsInStock($product)
    {
        $productStock = $this->stockRegistryInterface->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );

        if ($productStock->getManageStock()) {
            return (int)$productStock->getQty() > 0 && $productStock->getIsInStock();
        } else {
            return $productStock->getIsInStock();
        }
    }

    protected function getIsInStockGrouped($product)
    {
        $productStock = $this->stockRegistryInterface->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );
        return (int)$productStock->getIsInStock();
    }
}
