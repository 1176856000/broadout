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
 * @package     Plumrocket_DataFeedGenerator
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Datagenerator\Model\Feed\Resolver\Field;

use Magento\Bundle\Model\Product\Type as BundleProductType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Catalog\Pricing\Price\SpecialPrice;
use Magento\Framework\Exception\NotFoundException;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;
use Magento\Store\Api\Data\StoreInterface;
use Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

class Price implements FieldResolverInterface
{
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     */
    public function __construct(CatalogHelper $catalogHelper)
    {
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(
        TagInterface $tag,
        ProductInterface $product,
        StoreInterface $store,
        array $data,
        array $params = []
    ): string {
        $regularPrice = $this->getPrice($product, RegularPrice::PRICE_CODE);

        switch ($tag->getPropertyName()) {
            case 'price':
                return (string) round($regularPrice, 2);

            case 'final_price':
                return (string) round($this->getPrice($product, FinalPrice::PRICE_CODE), 2);

            case 'special_price':
                $specialPrice = $this->getPrice($product, SpecialPrice::PRICE_CODE);
                $specialPrice = $specialPrice > 0 ? $specialPrice : $regularPrice;
                return (string) round($specialPrice, 2);

            case 'price_with_currency':
                $price = number_format(round($regularPrice, 2), 2);
                return "$price {$store->getCurrentCurrencyCode()}";

            case 'price_with_tax':
                return (string) $this->catalogHelper->getTaxPrice($product, $regularPrice, true);

            case 'price_without_tax':
                return (string) $this->catalogHelper->getTaxPrice($product, $regularPrice, false);

            default:
                throw new NotFoundException($params['notFoundMessage']);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $priceCode
     * @return float
     */
    private function getPrice(Product $product, string $priceCode): float
    {
        if ($product->getTypeId() === BundleProductType::TYPE_CODE) {
            if (in_array($priceCode, [RegularPrice::PRICE_CODE, FinalPrice::PRICE_CODE], true)) {
                /** @var \Magento\Bundle\Pricing\Price\BundleRegularPrice $regularPriceModel */
                $regularPriceModel = $product->getPriceInfo()->getPrice($priceCode);
                return (float) $regularPriceModel->getMinimalPrice()->getValue();
            }

            /** @var \Magento\Bundle\Pricing\Price\SpecialPrice $regularPriceModel */
            $regularPriceModel = $product->getPriceInfo()->getPrice($priceCode);
            return (float) $regularPriceModel->getValue();
        }

        // Grouped product does not have prices except final
        if ($product->getTypeId() === GroupedProductType::TYPE_CODE) {
            return (float) $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();
        }

        return (float) $product->getPriceInfo()->getPrice($priceCode)->getValue();
    }
}
