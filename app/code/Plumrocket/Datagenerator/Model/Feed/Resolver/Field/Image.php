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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Product;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Api\Data\StoreInterface;
use Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

class Image implements FieldResolverInterface
{
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $productHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @param \Magento\Catalog\Helper\Product          $productHelper
     * @param \Magento\Catalog\Helper\Image            $imageHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        Product $productHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        Repository $assetRepo
    ) {
        $this->productHelper = $productHelper;
        $this->assetRepo = $assetRepo;
        $this->imageHelper = $imageHelper;
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
        if (isset($tag->getModifiers()['image_id'])) {
            $imageId = $tag->getModifiers()['image_id'];
            switch ($tag->getPropertyName()) {
                case 'thumbnail_url':
                    if (! $product->getThumbnail()) {
                        return $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
                    }
                    $imageFile = $product->getThumbnail();
                    break;
                case 'image_url':
                    if (! $product->getImage()) {
                        return $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
                    }
                    $imageFile = $product->getImage();
                    break;
                case 'small_image_url':
                    if (! $product->getSmallImage()) {
                        return $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/small_image.jpg');
                    }
                    $imageFile = $product->getSmallImage();
                    break;
                default:
                    throw new NotFoundException($params['notFoundMessage']);
            }

            return $this->imageHelper
                ->init($product, $imageId)
                ->setImageFile($imageFile)
                ->getUrl();
        }

        switch ($tag->getPropertyName()) {
            case 'thumbnail_url':
                $attributeThumbnail = $product->getResource()->getAttribute('thumbnail');
                return (string) $attributeThumbnail->getFrontend()->getUrl($product);
            case 'image_url':
                return (string) $this->productHelper->getImageUrl($product);
            case 'small_image_url':
                return (string) $this->productHelper->getSmallImageUrl($product);
            default:
                throw new NotFoundException($params['notFoundMessage']);
        }
    }
}
