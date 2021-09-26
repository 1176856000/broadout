<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model\ResourceModel\Review;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;

class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * @param $exportAttributeCodes
     * @throws LocalizedException
     */
    public function prepareForExport($exportAttributeCodes)
    {
        $this->createSelect($this->getColumns($exportAttributeCodes));
    }

    /**
     * @param $columns
     */
    private function createSelect($columns)
    {
        $this->getSelect()->reset(Select::COLUMNS);
        $this->getSelect()
            ->columns($columns)
            ->joinLeft(
                ['store' => $this->getTable('review_store')],
                'main_table.review_id = store.review_id',
                null
            )
            ->joinLeft(
                ['status' => $this->getTable('review_status')],
                'main_table.status_id = status.status_id',
                null
            )
            ->joinLeft(
                ['rating' => $this->getTable('rating_option_vote')],
                'main_table.review_id = rating.review_id',
                null
            )
            ->joinLeft(
                ['rstore' => $this->getTable('rating_store')],
                'rating.rating_id = rstore.rating_id',
                null
            )
            ->joinLeft(
                ['amvote' => $this->getTable('amasty_advanced_review_vote')],
                'amvote.review_id = main_table.review_id',
                null
            )
            ->joinLeft(
                ['reviewimage' => $this->getTable('amasty_advanced_review_images')],
                'reviewimage.review_id = main_table.review_id',
                null
            )
            ->group('main_table.review_id')
            ->order('main_table.review_id DESC');
    }

    /**
     * @param $exportAttributeCodes
     * @return array
     * @throws LocalizedException
     */
    private function getColumns($exportAttributeCodes)
    {
        $columns = [];
        foreach ($exportAttributeCodes as $attributeCode) {
            switch ($attributeCode) {
                case 'store_ids':
                    $columns[] = 'GROUP_CONCAT(DISTINCT(store.store_id)) as store_ids';
                    break;
                case 'rating_ids':
                    $columns[] = 'GROUP_CONCAT(DISTINCT(rating.rating_id)) as rating_ids';
                    break;
                case 'option_ids':
                    $columns[] = 'GROUP_CONCAT(DISTINCT(rating.option_id)) as option_ids';
                    break;
                case 'likes':
                    $columns[] = 'GROUP_CONCAT(DISTINCT CONCAT(amvote.ip,",",amvote.type) SEPARATOR ";") as likes';
                    break;
                case 'title':
                case 'detail':
                case 'customer_id':
                case 'like_about':
                case 'not_like_about':
                case 'guest_email':
                case 'nickname':
                    $columns[] = 'detail.' . $attributeCode;
                    break;
                case 'image':
                    $columns[] = 'IF(reviewimage.image_id, GROUP_CONCAT(DISTINCT reviewimage.path),NULL) as image';
                    break;
                default:
                    $columns[] = $attributeCode;
            }
        }

        if (!$columns) {
            throw new LocalizedException(__('Nothing to Export'));
        }

        return $columns;
    }
}
