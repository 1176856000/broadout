<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Test\Unit\Model\Import\Behaviors;

use Amasty\ReviewsImportExport\Model\Import\Behaviors\AddUpdate;
use Amasty\ReviewsImportExport\Test\Unit\Traits;
use Magento\Review\Model\ReviewFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AddUpdateTest
 *
 * @see AddUpdate
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class AddUpdateTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers AddUpdate::execute
     */
    public function testExecute()
    {
        $model = $this->getMockBuilder(AddUpdate::class)
            ->setMethods(['saveReview'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $reviewFactory = $this->createMock(ReviewFactory::class);
        $reviewCollection = $this->createMock(\Magento\Review\Model\ResourceModel\Review::class);
        $review = $this->getObjectManager()->getObject(\Magento\Review\Model\Review::class, []);
        $dataObject = $this->getObjectManager()->getObject(\Magento\Framework\DataObject::class, []);
        $dataObjectFactory = $this->createMock(\Magento\Framework\DataObjectFactory::class);

        $model->expects($this->exactly(2))->method('saveReview')->willReturn($review);
        $reviewFactory->expects($this->any())->method('create')->willReturn($review);
        $dataObjectFactory->expects($this->any())->method('create')->willReturn($dataObject);
        $reviewCollection->expects($this->any())->method('load')->willReturn($review, $this->returnCallback(
            function ($review, $id) {
                $review->setId($id);
            }
        ));

        $this->setProperty($model, 'reviewFactory', $reviewFactory);
        $this->setProperty($model, 'reviewCollection', $reviewCollection);
        $this->setProperty($model, 'dataObjectFactory', $dataObjectFactory);

        $params = [['review_id' => 1], ['review_id' => 2]];
        $this->assertEquals(
            ['count_items_created' => 1, 'count_items_updated' => 1],
            $this->invokeMethod($model, 'execute', [$params])->getData()
        );
    }
}
