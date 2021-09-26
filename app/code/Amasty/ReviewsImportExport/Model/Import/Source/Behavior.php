<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import\Source;

use Magento\ImportExport\Model\Source\Import\AbstractBehavior;
use Magento\ImportExport\Model\Import;

class Behavior extends AbstractBehavior
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_CUSTOM => __('Add'),
            Import::BEHAVIOR_ADD_UPDATE => __('Add/Update'),
            Import::BEHAVIOR_DELETE => __('Delete')
        ];
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return 'reviewsbasic';
    }
}
