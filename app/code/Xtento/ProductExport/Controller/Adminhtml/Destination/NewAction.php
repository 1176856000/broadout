<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            74lh4gf/3iR4FOCfgHiVeQv8i+kXFfeTLzNvj9f+ewI=
 * Last Modified: 2016-04-14T15:37:35+00:00
 * File:          app/code/Xtento/ProductExport/Controller/Adminhtml/Destination/NewAction.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Controller\Adminhtml\Destination;

class NewAction extends \Xtento\ProductExport\Controller\Adminhtml\Destination
{
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $result->forward('edit');
    }
}