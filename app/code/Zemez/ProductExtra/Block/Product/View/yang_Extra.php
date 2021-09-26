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
    public function getSomething()
    {
        return 'something';
    }
}

