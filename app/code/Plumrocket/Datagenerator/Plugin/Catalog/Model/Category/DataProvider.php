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

namespace Plumrocket\Datagenerator\Plugin\Catalog\Model\Category;

use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Plumrocket\Datagenerator\Model\ResourceModel\Template\CollectionFactory;
use Plumrocket\Datagenerator\Model\Template\Information;

class DataProvider
{
    /**
     * @var CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var Information
     */
    private $templateInformation;

    /**
     * DataProvider constructor.
     *
     * @param CollectionFactory $templateCollectionFactory
     * @param Information $templateInformation
     */
    public function __construct(CollectionFactory $templateCollectionFactory, Information $templateInformation)
    {
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->templateInformation = $templateInformation;
    }

    /**
     * @param CategoryDataProvider $subject
     * @param $result
     * @return mixed
     */
    public function afterGetMeta(CategoryDataProvider $subject, $result)
    {
        $templateCollection = $this->templateCollectionFactory->create();
        $templateCollection->addFieldToFilter('template_id', $this->templateInformation->getGoogleShoppingTemplateId());

        if ($templateCollection->count() === 0) {
            $result['pl_datagenerator']['children']['google_product_category']['arguments']['data']['config']
                = ['visible' => false];
        }

        return $result;
    }
}
