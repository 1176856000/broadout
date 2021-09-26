<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import\Validation;

use Amasty\Base\Model\Import\Validation\Validator;
use Amasty\ReviewsImportExport\Api\Data\ReviewInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

class Basic extends Validator implements \Amasty\Base\Model\Import\Validation\ValidatorInterface
{
    const ERROR_EMPTY_REVIEW_ID = 'emptyReviewId';
    const ERROR_COL_TITLE = 'titleEmpty';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::ERROR_EMPTY_REVIEW_ID => 'Warning! Empty Review Id',
        self::ERROR_COL_TITLE => '<b>Error!</b> Title Field Is Empty',
    ];

    /**
     * @param array $rowData
     * @param string $behavior
     * @return array|bool
     * @throws \Amasty\Base\Exceptions\StopValidation
     */
    public function validateRow(array $rowData, $behavior)
    {
        $this->errors = [];
        if ($behavior === \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
            if (empty($rowData[ReviewInterface::REVIEW_ID])) {
                $this->errors[self::ERROR_EMPTY_REVIEW_ID] = ProcessingError::ERROR_LEVEL_NOT_CRITICAL;
            }

            throw new \Amasty\Base\Exceptions\StopValidation(parent::validateResult());
        }

        return parent::validateResult();
    }
}
