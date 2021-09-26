<?php

/**
 *
 * Copyright © 2015 Zemez. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Zemez\FilmSlider\Model\ResourceModel\SliderItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Zemez\FilmSlider\Model\SliderItem',
            'Zemez\FilmSlider\Model\ResourceModel\SliderItem');
    }

    public function _applyBySliderFilter($slider)
    {
        $this->getSelect()
            ->where('status=1 and parent_id = ?', $slider->getId())
            ->order('sort_item ASC')
            ->order('slideritem_id ASC');
        return $this;
    }
}
