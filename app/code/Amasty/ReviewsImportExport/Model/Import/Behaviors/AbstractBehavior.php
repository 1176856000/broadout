<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\Import\Behaviors;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractBehavior implements \Amasty\Base\Model\Import\Behavior\BehaviorInterface
{
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review
     */
    protected $reviewCollection;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating
     */
    protected $ratingCollection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection
     */
    protected $ratingOptionCollection;

    /**
     * @var \Amasty\AdvancedReview\Api\VoteRepositoryInterface
     */
    protected $voteRepository;

    /**
     * @var \Amasty\AdvancedReview\Api\ImagesRepositoryInterface
     */
    protected $imagesRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    public function __construct(
        ReviewFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Review $reviewCollection,
        StoreManagerInterface $storeManager,
        RatingFactory $ratingFactory,
        \Magento\Review\Model\ResourceModel\Rating $ratingCollection,
        \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection $ratingOptionCollection,
        \Amasty\AdvancedReview\Api\VoteRepositoryInterface $voteRepository,
        \Amasty\AdvancedReview\Api\ImagesRepositoryInterface $imagesRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->storeManager = $storeManager;
        $this->reviewCollection = $reviewCollection;
        $this->ratingCollection = $ratingCollection;
        $this->ratingOptionCollection = $ratingOptionCollection;
        $this->voteRepository = $voteRepository;
        $this->imagesRepository = $imagesRepository;
        $this->logger = $logger;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param $review
     * @param $data
     * @param bool $isUpdate
     */
    protected function saveReview($review, $data, $isUpdate = false)
    {
        try {
            $this->setReviewData($review, $data);
            $this->reviewCollection->save($review);
            $reviewId = $review->getId();
            $this->saveRating($reviewId, $data, $isUpdate);
            $this->saveVotes($reviewId, $data);
            $this->saveImages($reviewId, $data);
            $review->aggregate();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $review
     * @param $data
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function setReviewData($review, $data)
    {
        if ($this->storeManager->hasSingleStore()) {
            $data['stores'] = [$this->storeManager->getStore(true)->getId()];
        } elseif (isset($data['store_ids'])) {
            $data['stores'] = [];
            foreach (explode(',', $data['store_ids'] ?? '') as $storeId) {
                try {
                    if ($this->storeManager->getStore($storeId)) {
                        $data['stores'][] = $storeId;
                    }
                } catch (NoSuchEntityException $e) {
                    continue;
                }
            }
        }
        $review->setData($data)->setEntityId(1); //1-product entity, magento constant is missing
    }

    /**
     * @param int $reviewId
     * @param array $reviewData
     * @param bool $isUpdate
     */
    protected function saveRating($reviewId, $reviewData, $isUpdate)
    {
        $voteId = $isUpdate
            ? $this->ratingOptionCollection->getItemByColumnValue('review_id', $reviewId)
            : null;

        $optionIds = isset($reviewData['option_ids']) && $reviewData['option_ids']
            ? explode(',', $reviewData['option_ids'])
            : false;
        $ratingIds = isset($reviewData['rating_ids']) && $reviewData['rating_ids']
            ? explode(',', $reviewData['rating_ids'])
            : false;
        if ($optionIds && $ratingIds) {
            $rating = $this->ratingFactory->create()->setReviewId($reviewId);
            foreach ($optionIds as $key => $id) {
                if ($voteId) {
                    $rating->setVoteId($voteId)->updateOptionVote($id);
                } else {
                    $rating->setRatingId($ratingIds[$key])
                        ->setReviewId($reviewId)
                        ->setCustomerId($reviewData['customer_id'] ?? 0)
                        ->addOptionVote($id, $reviewData['entity_pk_value'] ?? 0);
                }
            }
        }
    }

    /**
     * @param int $reviewId
     * @param array $reviewData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function saveVotes($reviewId, $reviewData)
    {
        if (isset($reviewData['likes']) && $reviewData['likes']) {
            $voteArray = array_flip($this->voteRepository->getVoteIpKeys());
            foreach (explode(';', $reviewData['likes']) as $likeAndIpArray) {
                $likeAndIp = explode(',', $likeAndIpArray);
                if (count($likeAndIp) == 2 && !isset($voteArray[$reviewId . $likeAndIp[0]])) {
                    $this->voteRepository->save(
                        $this->voteRepository->getVoteModel()
                            ->setReviewId($reviewId)
                            ->setType($likeAndIp[1])
                            ->setIp($likeAndIp[0])
                    );
                }
            }
        }
    }

    /**
     * @param int $reviewId
     * @param array $reviewData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function saveImages($reviewId, $reviewData)
    {
        if (isset($reviewData['image']) && $reviewData['image']) {
            $pathsArray = array_flip($this->imagesRepository->getImageKeys());
            foreach (explode(',', $reviewData['image']) as $path) {
                if (!isset($pathsArray[$reviewId . $path])) {
                    $this->imagesRepository->save(
                        $this->imagesRepository->getImageModel()->setReviewId($reviewId)->setPath($path)
                    );
                }
            }
        }
    }
}
