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

namespace Plumrocket\Datagenerator\Model\Feed\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Api\Data\StoreInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

class FieldComposite implements FieldResolverInterface
{
    /**
     * @var FieldResolverInterface[]
     */
    private $fieldResolvers;

    /**
     * @param array $fieldResolvers
     */
    public function __construct(array $fieldResolvers = [])
    {
        $this->fieldResolvers = $fieldResolvers;
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
    ) {
        $propertyName = $tag->getPropertyName();

        if (isset($this->fieldResolvers[$propertyName])) {
            $params['notFoundMessage'] = __(
                'Plumrocket Data Feed Generator: Resolver %1 can not resolve field: %1',
                get_class($this->fieldResolvers[$propertyName]),
                $propertyName
            );
            return $this->fieldResolvers[$propertyName]->resolve($tag, $product, $store, $data, $params);
        }

        throw new NotFoundException(
            __('Plumrocket Data Feed Generator: Cannot find resolver for field: %1', $propertyName)
        );
    }
}
