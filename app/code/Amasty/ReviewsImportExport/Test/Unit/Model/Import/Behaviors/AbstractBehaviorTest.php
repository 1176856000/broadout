<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Test\Unit\Model\Import\Behaviors;

use Amasty\ReviewsImportExport\Model\Import\Behaviors\AbstractBehavior;
use Amasty\ReviewsImportExport\Test\Unit\Traits;
use Magento\Review\Model\RatingFactory;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AbstractBehaviorTest
 *
 * @see AbstractBehavior
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class AbstractBehaviorTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var MockObject
     */
    private $model;

    protected function setUp()
    {
        $this->model = $this->getMockBuilder(AbstractBehavior::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @covers AbstractBehavior::setReviewData
     */
    public function testSetReviewData()
    {
        $storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->setMethods(['hasSingleStore', 'getStore', 'getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $review = $this->getObjectManager()->getObject(\Magento\Review\Model\Review::class, []);

        $storeManager->expects($this->any())->method('hasSingleStore')->willReturnOnConsecutiveCalls(true, false);
        $storeManager->expects($this->any())->method('getStore')->willReturn($storeManager);
        $storeManager->expects($this->once())->method('getId')->willReturn(5);

        $this->setProperty($this->model, 'storeManager', $storeManager);

        $this->invokeMethod($this->model, 'setReviewData', [$review, ['store_ids' => '1,2']]);
        $result = ['store_ids' => '1,2', 'stores' => [5], 'entity_id' => 1];
        $this->assertEquals($result, $review->getData());
        $review = $this->getObjectManager()->getObject(\Magento\Review\Model\Review::class, []);
        $this->invokeMethod($this->model, 'setReviewData', [$review, ['store_ids' => '1,2']]);
        $result = ['store_ids' => '1,2', 'stores' => [1, 2], 'entity_id' => 1];
        $this->assertEquals($result, $review->getData());
    }

    /**
     * @covers AbstractBehavior::saveRating
     */
    public function testSaveRating()
    {
        $ratingFactory = $this->createMock(RatingFactory::class);
        $ratingOptionFactory = $this->createMock(\Magento\Review\Model\Rating\OptionFactory::class);
        $ratingOptionCollection = $this->createMock(\Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection::class);
        $resource = $this->createMock(\Magento\Review\Model\ResourceModel\Rating\Option::class);
        $rating = $this->getObjectManager()->getObject(
            \Magento\Review\Model\Rating::class,
            ['_ratingOptionFactory' => $ratingOptionFactory]
        );
        $option = $this->getObjectManager()->getObject(\Magento\Review\Model\Rating\Option::class, []);

        $ratingFactory->expects($this->any())->method('create')->willReturn($rating);
        $ratingOptionFactory->expects($this->any())->method('create')->willReturn($option);
        $resource->expects($this->any())->method('addVote')->willReturn('');
        $ratingOptionCollection->expects($this->any())->method('getItemByColumnValue')->willReturn(5);

        $this->setProperty($this->model, 'ratingFactory', $ratingFactory);
        $this->setProperty($this->model, 'ratingOptionCollection', $ratingOptionCollection);
        $this->setProperty($option, '_resource', $resource);

        $data = ['rating' => 1, 'customer_id' => 2, 'entity_pk_value' => 3, 'option_ids' => '5,6', 'rating_ids' => '7,8'];
        $this->invokeMethod(
            $this->model,
            'saveRating',
            [1, $data, false]
        );

        $this->assertEquals(['review_id' => 1, 'rating_id' => '8', 'customer_id' => 2], $rating->getData());

        $this->invokeMethod(
            $this->model,
            'saveRating',
            [1, $data, true]
        );

        $this->assertEquals(
            ['review_id' => 1, 'rating_id' => '8', 'customer_id' => 2, 'vote_id' => 5],
            $rating->getData()
        );
    }
}
