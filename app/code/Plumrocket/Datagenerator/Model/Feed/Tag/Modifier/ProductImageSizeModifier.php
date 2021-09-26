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

declare(strict_types=1);

namespace Plumrocket\Datagenerator\Model\Feed\Tag\Modifier;

use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Resize product image
 *
 * Format: "|size:{width}[:{height}]"
 * For example:
 *  "|size:150"
 *  "|size:300:500"
 *
 * @since 2.3.0
 */
class ProductImageSizeModifier implements ModifierInterface
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $catalogImageHelper;

    /**
     * @param \Magento\Catalog\Helper\Image $catalogImageHelper
     */
    public function __construct(CatalogImageHelper $catalogImageHelper)
    {
        $this->catalogImageHelper = $catalogImageHelper;
    }

    /**
     * @inheritDoc
     */
    public function modify(TagInterface $tag, $propertyValue, string $paramsString = '', $entityObject = null)
    {
        $imgAttributes = ['image_url', 'thumbnail_url', 'small_image_url'];
        if (is_object($entityObject) && in_array($tag->getPropertyName(), $imgAttributes, true)) {
            $size = explode(':', $paramsString);
            $width = (int) $size[0];
            $height = ! empty($size[1]) ? (int) $size[1] : null;

            if ($width && 'product' === $tag->getEntityType()) {
                $imageType = 'new_products_content_widget_grid';
                if ('image_url' === $tag->getPropertyName()) {
                    $imageType = 'product_base_image';
                } elseif ('thumbnail_url' === $tag->getPropertyName()) {
                    $imageType = 'product_page_image_small';
                }

                $propertyValue = (string) $this->catalogImageHelper
                    ->init($entityObject, $imageType)
                    ->resize($width, $height)
                    ->getUrl();
            }
        }

        return $propertyValue;
    }
}
