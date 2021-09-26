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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Datagenerator\Controller\Adminhtml\Datagenerator;
use Plumrocket\Datagenerator\Model\TemplateFactory;

class Save extends Datagenerator
{
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param TemplateFactory $templateFactory
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        Context $context,
        TemplateFactory $templateFactory,
        CollectionFactory $categoryCollectionFactory
    ) {
        parent::__construct($context, $templateFactory);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave($model, $request)
    {
        $categoryMapping = $request->getParam('category_mapping', []);
        $affectedCategories = $this->getAffectedCategories($categoryMapping);
        $categoryCollection = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToSelect(['google_product_category', 'url_key'])
            ->setStoreId(Store::DEFAULT_STORE_ID);

        if (! empty($affectedCategories)) {
            $categoryCollection->addFieldToFilter('entity_id', ['in' => $affectedCategories]);

            foreach ($categoryCollection as $category) {
                $category->setData('google_product_category', $categoryMapping[$category->getId()])
                    ->setStoreId(Store::DEFAULT_STORE_ID);
            }

            $categoryCollection->save();
        }
    }

    /**
     * Retrieve ids of affected categories
     *
     * @param $data
     * @return array
     */
    private function getAffectedCategories($data)
    {
        return array_keys(array_filter($data, function ($value) {
            return (bool) $value;
        }));
    }
}
