<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block\Adminhtml\Creditmemo;

use Plumrocket\Affiliate\Block\Adminhtml\Order\Info as OrderInfoBlock;

class Info extends OrderInfoBlock
{
    public function getOrder()
    {
        return $this->_registry->registry('current_creditmemo')->getOrder();
    }
}
