<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ReviewsImportExport
 */


namespace Amasty\ReviewsImportExport\Model;

use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ImportExport\Model\Export as ModelExport;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Amasty\ReviewsImportExport\Model\ResourceModel\Review\CollectionFactory;
use Amasty\ReviewsImportExport\Api\ImportExport\ExportInterface;

class Export extends AbstractEntity
{
    const COLUMNS = [
        'review_id',
        'created_at',
        'entity_pk_value',
        'status_id',
        'answer',
        'verified_buyer',
        'is_recommended',
        'store_ids',
        'title',
        'detail',
        'nickname',
        'customer_id',
        'like_about',
        'not_like_about',
        'guest_email',
        'rating_ids',
        'option_ids',
        'likes',
        'image'
    ];

    /**
     * @var \Amasty\ReviewsImportExport\Model\ResourceModel\Review\Collection
     */
    protected $collection;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $exportAttributeCodes = [];

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ExportFactory $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        CollectionFactory $collection,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
        $this->collectionFactory = $collection;
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return ExportInterface::REVIEWS_EXPORT;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Review\Model\ResourceModel\Review\Collection
     * @throws LocalizedException
     */
    protected function _getEntityCollection()
    {
        if ($this->collection === null) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->prepareForExport($this->_getExportAttributeCodes());
        }

        return $this->collection;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $item
     * @throws LocalizedException
     */
    public function exportItem($item)
    {
        $row = $item->toArray();
        $this->getWriter()->writeRow($row);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function export()
    {
        $writer = $this->getWriter();
        $writer->setHeaderCols($this->_getHeaderColumns());
        $this->_exportCollectionByPages($this->_getEntityCollection());

        return $writer->getContents();
    }

    /**
     * @return array
     */
    protected function _getExportAttributeCodes()
    {
        if (!$this->exportAttributeCodes) {
            $skipAttr = $this->_parameters[ModelExport::FILTER_ELEMENT_SKIP];
            foreach (static::COLUMNS as $column) {
                if (array_search($column, $skipAttr) === false) {
                    $this->exportAttributeCodes[] = $column;
                }
            }
        }

        return $this->exportAttributeCodes;
    }

    /**
     * @return array
     */
    protected function _getHeaderColumns()
    {
        return $this->_getExportAttributeCodes();
    }
}
