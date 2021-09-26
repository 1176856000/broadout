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

namespace Plumrocket\Datagenerator\Ui\DataProvider\Catalog\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Eav implements ModifierInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Eav constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $meta['data-feed-generator']['arguments']['data']['config']['label'] = 'Data Feed Generator';

        $meta['data-feed-generator']['children']['container_google_product_category']
            ['children']['google_product_category']['arguments']['data']['config']['component']
            = 'Plumrocket_Datagenerator/js/form/element/category';

        $meta['data-feed-generator']['children']['container_google_product_category']
            ['children']['google_product_category']['arguments']['data']['config']['url']
            = $this->urlBuilder->getUrl('prdatagenerator/datagenerator/googleTaxonomy');

        return $meta;
    }
}
