<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

use Plumrocket\Affiliate\Model\ResourceModel\Affiliate;

/**
 * @method string|null getName()
 *
 * @since 2.8.0
 */
abstract class AbstractDataModel extends \Magento\Framework\Model\AbstractModel
{
    public const SECTION_HEAD      = 'head';
    public const SECTION_BODYBEGIN = 'bodybegin';
    public const SECTION_BODYEND   = 'bodyend';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Affiliate::class);
    }

    /**
     * Simulate Load
     * @param  \Plumrocket\Affiliate\Model\Affiliate $affiliate
     * @return $this
     */
    public function simulateLoad($affiliate)
    {
        $this->setData($affiliate->getData());
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * Get additional data array
     *
     * @return array
     */
    public function getAdditionalDataArray()
    {
        if (! $this->getAdditionalData()) {
            return $this->getDefaultAdditionalDataArray();
        }

        if (! is_array($this->getAdditionalData())) {
            return json_decode($this->getAdditionalData(), true);
        }

        return $this->getAdditionalData();
    }

    /**
     * Get affiliate program config
     *
     * @param string $key
     * @param array|string|int|null $default
     * @return array|string|int|null
     */
    public function getConfig(string $key, $default = '')
    {
        return $this->getAdditionalDataArray()[$key] ?? $default;
    }

    /**
     * Set additional data
     *
     * @param array $values
     * @return  $this
     */
    public function setAdditionalDataValues($values)
    {
        $data = $this->getAdditionalDataArray();

        foreach ($values as $key => $value) {
            $data[$key] = $value;
        }

        $this->setAdditionalData(json_encode($data));
        return $this;
    }

    /**
     * Get default additional data array
     *
     * @return array
     */
    public function getDefaultAdditionalDataArray(): array
    {
        return [];
    }

    /**
     * Set stores
     * @param array $storeArray
     * @return $this
     */
    public function setStores(array $storeArray)
    {
        if (in_array(0, $storeArray)) {
            $stores = 0;
        } else {
            $stores = ','.implode(',', $storeArray).',';
        }

        $this->setData('stores', $stores);
        return $this;
    }

    /**
     * Get stores
     * @return array
     */
    public function getStores()
    {
        if ($this->hasData('stores')) {
            if (is_array($this->getData('stores'))) {
                return $this->getData('stores');
            }
            return explode(',', $this->getData('stores'));
        }

        return [0];
    }
}
