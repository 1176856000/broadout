<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml\Affiliate;

class NewAction extends \Plumrocket\Affiliate\Controller\Adminhtml\Affiliate
{
    /**
     * {@inheritdoc}
     */
    protected function _newAction()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Add New Affiliate Program'));
        $this->_view->renderLayout();
    }
}
