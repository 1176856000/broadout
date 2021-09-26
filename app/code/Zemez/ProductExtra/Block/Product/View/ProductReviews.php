<?php
/**
 *
 * Copyright © 2019 Zemez. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Zemez\ProductExtra\Block\Product\View;


class ProductReviews extends \Magento\Framework\View\Element\Template //注意继承对象
{
    protected $_reviewCollectionFactory;
    protected $_storeManager;
    protected $_abstractProduct;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Block\Product\AbstractProduct $abstractProduct,
        array $data = []
    )
    {
        $this->_reviewCollectionFactory = $reviewCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_abstractProduct = $abstractProduct;
        parent::__construct($context, $data);
    }

    public function getCurrentStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    public function getReviewsCollection()
    {
        $currentStoreId = $this->getCurrentStoreId();

        $reviewsCollection = $this->_reviewCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addStoreFilter($currentStoreId)
            ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->setDateOrder()
            ->addRateVotes();

        return $reviewsCollection;
    }

    public function getPid(){
        $product = $this->_abstractProduct->getProduct();
        return $product->getId();
    }
}

