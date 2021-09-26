<?php
/**
 *
 * Copyright © 2019 Zemez. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Zemez\ProductExtra\Block\Product\View;

use Magento\Catalog\Block\Product\AbstractProduct;


class Extra extends AbstractProduct //注意继承对象
{

    //获取USD
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    public function getSomething()
    {
        return 'something';
    }

    //获取产品地址
    public function getReviewsUrl($useDirectLink = false)
    {
        $product = $this->getProduct();
        if ($useDirectLink) {
            return $this->getUrl(
                'review/product/list',
                ['id' => $product->getId(), 'category' => $product->getCategoryId()]
            );
        }
        return $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
    }
}

