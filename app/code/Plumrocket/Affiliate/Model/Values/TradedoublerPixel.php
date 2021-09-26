<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Values;

class TradedoublerPixel
{
    /**
     * Options
     * @var array
     */
    protected $_options = null;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_getOptions();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];
        foreach ($this->_getOptions() as $option) {
            $options[ $option['value'] ] = $option['label'];
        }

        return $options;
    }

    /**
     * Get options
     * @return array
     */
    protected function _getOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => 0, 'label' => __('Disable') ],
                ['value' => 1, 'label' => __('Enable (Sale Tracking Pixel)') ],
                ['value' => 2, 'label' => __('Enable (PLT, Product Level Tracking)') ],
            ];
        }

        return $this->_options;
    }
}
