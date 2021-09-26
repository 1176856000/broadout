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
use Magento\Framework\Data\Collection;
use Magento\Store\Api\Data\StoreInterface;
use Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Retrieve list of gallery images except first one
 *
 * @since 2.3.0
 */
class MediaGallery implements FieldResolverInterface
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(\Magento\Catalog\Helper\Image $imageHelper)
    {
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
    ): array {
        $images = $product->getMediaGalleryImages();
        if (!$images instanceof Collection) {
            return [];
        }

        $imageId = $tag->getModifiers()['image_id'] ?? 'product_page_image_medium_no_frame';

        $urls = [];
        /** @var \Magento\Framework\DataObject $image */
        foreach ($images as $image) {
            if ($product->getImage() === $image->getFile()) { // google feed: skip image duplicate
                continue;
            }

            $urls[] = [
                'url' => $this->imageHelper
                    ->init($product, $imageId)
                    ->setImageFile($image->getFile())
                    ->getUrl()
            ];
        }

        return $urls;
    }
}
