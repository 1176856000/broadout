<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Flexoffers extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function getAdvertiserId()
    {
        return $this->getAdditionalDataValue('advertiser_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = '';

        if ($_section == parent::SECTION_BODYEND) {
            $order = $this->getLastOrder();
            if ($order && $order->getId()) {

                $amount = round($order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(), 2);
                $src = 'https://track.flexlinks.com/tracker.aspx?AID='.$this->getAdvertiserId()
                    . "&AMT=" .$amount
                    . "&UID=" . $order->getIncrementId();

                $html = '
                    <!-- BEGIN OF FLEXOFFERS.COM TRACKING CODE -->
                    <img src="'.$src.'" width="1" height="1" alt="" />
                    <!-- END OF FLEXOFFERS.COM TRACKING CODE -->
                ';

            }
        }
        return $html;
    }
}
