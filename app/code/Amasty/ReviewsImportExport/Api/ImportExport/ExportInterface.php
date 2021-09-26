<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Api\ImportExport;

interface ExportInterface
{
    const REVIEWS_EXPORT = 'reviews_export';
    const EXPORT_TYPES = [self::REVIEWS_EXPORT];
    const BLOCK_NAME = 'reviews.export';
}
