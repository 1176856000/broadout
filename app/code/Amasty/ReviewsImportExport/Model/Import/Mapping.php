<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import;

use Amasty\Base\Model\Import\Mapping\Mapping as MappingBase;
use Amasty\ReviewsImportExport\Api\Data\ReviewInterface;
use Amasty\ReviewsImportExport\Model\Export;

class Mapping extends MappingBase implements \Amasty\Base\Model\Import\Mapping\MappingInterface
{
    /**
     * @var array
     */
    protected $mappings = Export::COLUMNS;

    /**
     * @var string
     */
    protected $masterAttributeCode = ReviewInterface::TITLE;
}
