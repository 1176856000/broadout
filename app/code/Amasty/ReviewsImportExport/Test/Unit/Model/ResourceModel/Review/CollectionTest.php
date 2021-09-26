<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Test\Unit\Model\ResourceModel\Review;

use Amasty\ReviewsImportExport\Model\Export;
use Amasty\ReviewsImportExport\Model\ResourceModel\Review\Collection;
use Amasty\ReviewsImportExport\Test\Unit\Traits;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\ImportExport\Model\Export as ModelExport;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class CollectionTest
 *
 * @see Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class CollectionTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Collection::getColumns
     * @throws \ReflectionException
     */
    public function testGetColumns()
    {
        $model = $this->createPartialMock(Collection::class, []);

        $this->setProperty($model, '_parameters', [ModelExport::FILTER_ELEMENT_SKIP => []], Export::class);

        $result = [
            'review_id',
            'created_at',
            'entity_pk_value',
            'status_id',
            'answer',
            'verified_buyer',
            'is_recommended',
            'GROUP_CONCAT(DISTINCT(store.store_id)) as store_ids',
            'detail.title',
            'detail.detail',
            'detail.nickname',
            'detail.customer_id',
            'detail.like_about',
            'detail.not_like_about',
            'detail.guest_email',
            'GROUP_CONCAT(DISTINCT(rating.rating_id)) as rating_ids',
            'GROUP_CONCAT(DISTINCT(rating.option_id)) as option_ids',
            'GROUP_CONCAT(DISTINCT CONCAT(amvote.ip,",",amvote.type) SEPARATOR ";") as likes',
            'IF(reviewimage.image_id, GROUP_CONCAT(DISTINCT reviewimage.path),NULL) as image',
        ];

        $this->assertEquals($result, $this->invokeMethod($model, 'getColumns', [Export::COLUMNS]));
    }
}
