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

namespace Plumrocket\Datagenerator\Model\Feed\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Api\Data\StoreInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Retrieve value of product property (attr, field, custom)
 *
 * @since 2.3.0
 */
interface FieldResolverInterface
{
    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface                         $tag
     * @param \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Api\Data\StoreInterface                                    $store
     * @param array                                                                     $data
     * @param array                                                                     $params
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function resolve(
        TagInterface $tag,
        ProductInterface $product,
        StoreInterface $store,
        array $data,
        array $params = []
    );
}
