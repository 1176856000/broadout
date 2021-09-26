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
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Api\Data\StoreInterface;
use Plumrocket\Datagenerator\Model\Feed\Resolver\FieldResolverInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

class Description implements FieldResolverInterface
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     */
    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
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
        //Magento 2 haven't attribute short description
        //That's why we trim description
        if ($tag->getPropertyName() === 'short_description') {
            if (! isset($data['short_description'])) {
                if (isset($data['description'])) {
                    return $this->filterManager->truncate(
                        strip_tags($data['description']),
                        ['length' => 255, 'etc' => '...', 'remainder' => '', 'breakWords' => false]
                    );
                }

                return '';
            }

            return (string) $data['short_description'];
        }

        throw new NotFoundException($params['notFoundMessage']);
    }
}
