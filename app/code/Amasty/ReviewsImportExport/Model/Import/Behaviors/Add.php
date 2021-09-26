<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import\Behaviors;

class Add extends AbstractBehavior
{
    /**
     * @param array $importData
     * @return int
     */
    public function execute(array $importData)
    {
        $createdCount = 0;
        foreach ($importData as $data) {
            unset($data['review_id']);
            $review = $this->reviewFactory->create();
            $this->saveReview($review, $data);
            $createdCount++;
        }

        return $this->dataObjectFactory->create()->setCountItemsCreated($createdCount);
    }
}
