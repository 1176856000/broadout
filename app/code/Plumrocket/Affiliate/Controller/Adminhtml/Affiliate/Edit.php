<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Controller\Adminhtml\Affiliate;

class Edit extends \Plumrocket\Affiliate\Controller\Adminhtml\Affiliate
{
    public function _editAction()
    {
        $model = $this->_getModel();

        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu($this->_activeMenu);

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit '.$this->_objectTitle);
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New '.$this->_objectTitle);
            $breadcrumbLabel = __('Create '.$this->_objectTitle);
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__($this->_objectTitle));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId()
                ? __('Edit Affiliate Program - "%1"', $model->getName())
                : __('New Affiliate Program')
        );

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        $values = $this->_getSession()->getData($this->_formSessionKey, true);
        if ($values) {
            $model->addData($values);
        }

        $this->_view->renderLayout();
    }
}
