<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Order;

use Magento\Framework\Model\AbstractModel;

class Info extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Plumrocket\Affiliate\Model\ResourceModel\Order\Info');
    }
}
