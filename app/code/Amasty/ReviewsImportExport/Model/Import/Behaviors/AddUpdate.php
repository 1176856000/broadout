<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import\Behaviors;

class AddUpdate extends AbstractBehavior
{
    /**
     * @param array $importData
     * @return array|void
     */
    public function execute(array $importData)
    {
        $createdCount = 0;
        $updatedCount = 0;
        foreach ($importData as $reviewData) {
            $review = $this->reviewFactory->create();
            $this->reviewCollection->load($review, $reviewData['review_id'] ?? 0);
            if ($review->getId()) {
                $this->saveReview($review, $reviewData, true);
                $updatedCount++;
            } else {
                if ($reviewData['review_id']) {
                    unset($reviewData['review_id']);
                }
                $review = $this->reviewFactory->create();
                $this->saveReview($review, $reviewData);
                $createdCount++;
            }
        }

        return $this->dataObjectFactory->create()
            ->setCountItemsCreated($createdCount)
            ->setCountItemsUpdated($updatedCount);
    }
}
