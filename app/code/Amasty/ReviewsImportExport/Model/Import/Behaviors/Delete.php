<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import\Behaviors;

class Delete extends AbstractBehavior
{
    /**
     * @param array $importData
     * @return int|void
     * @throws \Exception
     */
    public function execute(array $importData)
    {
        $deletedCount = 0;
        foreach ($importData as $reviewData) {
            $review = $this->reviewFactory->create();
            $this->reviewCollection->load($review, $reviewData['review_id']);
            $this->reviewCollection->delete($review);
            $vote = $this->voteRepository->getByIdAndIp($reviewData['review_id'], $reviewData['ip'] ?? 0);
            $this->voteRepository->delete($vote);
            if (isset($reviewData['image'])) {
                $this->imagesRepository->deleteByReviewId($reviewData['review_id']);
            }
            $deletedCount++;
        }

        return $this->dataObjectFactory->create()->setCountItemsDeleted($deletedCount);
    }
}
