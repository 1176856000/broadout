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

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Plumrocket\Datagenerator\Model\ResourceModel\Template\CollectionFactory;
use Plumrocket\Datagenerator\Model\Template\Information;

class GoogleCategory implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var Information
     */
    private $templateInformation;

    /**
     * GoogleCategory constructor.
     *
     * @param ArrayManager $arrayManager
     * @param CollectionFactory $templateCollectionFactory
     * @param Information $templateInformation
     */
    public function __construct(
        ArrayManager $arrayManager,
        CollectionFactory $templateCollectionFactory,
        Information $templateInformation
    ) {
        $this->arrayManager = $arrayManager;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->templateInformation = $templateInformation;
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
        $templateCollection = $this->templateCollectionFactory->create();
        $templateCollection->addFieldToFilter('template_id', $this->templateInformation->getGoogleShoppingTemplateId());

        if ($templateCollection->count() === 0) {
            $attributeCode = 'google_product_category';
            $attributePath = $this->arrayManager->findPath($attributeCode, $meta);

            if (! $attributePath) {
                return $meta;
            }

            $meta = $this->arrayManager->merge(
                [$attributePath, 'arguments/data/config'],
                $meta,
                [
                    'visible' => false,
                ]
            );
        }

        return $meta;
    }
}
