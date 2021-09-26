<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Shopzilla extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        switch ($_section) {
            case parent::SECTION_BODYBEGIN:
                $order = $this->getLastOrder();

                if ($order && $order->getId()) {

                    $itemsCount = 0;
                    foreach ($order->getAllVisibleItems() as $item) {
                        $itemsCount += $item->getQtyOrdered();
                    }

                    $customerType = $this->isNewCustomer($order) ? 1 : 0;

                    return '
                        <style type="text/css">
                            body > img[width="1"] {
                                position: absolute;
                            }
                        </style>
                        <script language="javascript">
                        /* Performance Tracking Data */
                        var mid = "'.htmlspecialchars($this->getMerchantId()).'";
                        var cust_type = "'.$customerType.'";
                        var order_value = "'.$order->getSubtotal().'";
                        var order_id = "'.htmlspecialchars($order->getIncrementId()).'";
                        var units_ordered = "'.$itemsCount.'";
                        </script>
                        <script language="javascript" src="https://images.bizrate.com/api/roi_tracker.min.js?v=1"></script>';
                }
                break;
            default:
                return null;
        }
    }
}
