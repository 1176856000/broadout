<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Pepperjam extends AbstractModel
{
    const TYPE_ID = 20;

    const STORAGE_NAME = 'PEPPERJAM';

    /**
     * Retrieve program id
     *
     * @return string
     */
    public function getProgramId()
    {
        return $this->getAdditionalDataValue('program_id');
    }

    /**
     * Retrieve tracking type
     *
     * @return string
     */
    public function getTrackingType()
    {
        return $this->getAdditionalDataValue('tracking_type');
    }

    /**
     * Retrieve transaction type
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->getAdditionalDataValue('transaction_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = '';

        if (($_section == parent::SECTION_BODYEND) && ($order = $this->getLastOrder())) {
            $srcParams = [];
            $cookieClickIds = json_decode($this->_cookieManager->getCookie(self::STORAGE_NAME));
            $cookieClickIds = is_array($cookieClickIds) ? $cookieClickIds : [];
            $clickId = implode(',', $cookieClickIds);

            switch ($this->getTrackingType()) {
                case 'basic':
                    /**
                     * Add base parameters
                     */
                    $srcParams = [
                        'PID=' . $this->getProgramId(),
                        'AMOUNT=' . round($order->getGrandTotal() - $order->getShippingAmount() - $order->getTaxAmount(), 2),
                        'TYPE=' . $this->getTransactionType(),
                        'OID=' . $order->getIncrementId(),
                        'CLICK_ID=' . $clickId
                    ];

                    /**
                     * Add optional parametrs
                     */
                    if ($promocode = $order->getCouponCode()) {
                        $srcParams[] = 'PROMOCODE=' . urlencode($promocode);
                    }
                    break;
                case 'dynamic':
                    $n = 1;
                    $products = [
                        'ITEM_ID' => [],
                        'ITEM_PRICE' => [],
                        'QUANTITY' => [],
                    ];

                    foreach ($order->getAllVisibleItems() as $item) {
                        $products['ITEM_ID'][$n]    = $item->getSku();
                        $products['ITEM_PRICE'][$n] = round($item->getPrice(), 2);
                        $products['QUANTITY'][$n]   = round($item->getQtyOrdered());
                        $n++;
                    }

                    /**
                     * Add base parameters
                     */
                    $srcParams = [
                        'INT=DYNAMIC',
                        'PROGRAM_ID=' . $this->getProgramId(),
                        'ORDER_ID=' . $order->getIncrementId(),
                        'CLICK_ID=' . $clickId,
                        http_build_query($products['ITEM_ID'], 'ITEM_ID'),
                        http_build_query($products['ITEM_PRICE'], 'ITEM_PRICE'),
                        http_build_query($products['QUANTITY'], 'QUANTITY'),
                        'NEW_TO_FILE=' . ($this->isNewCustomer($order) ? 1 : 0),
                    ];

                    /**
                     * Add optional parametrs
                     */
                    if ($promocode = $order->getCouponCode()) {
                        $srcParams[] = 'COUPON=' . urlencode($promocode);
                    }
                    break;
                default:
                //case 'itemized'
                    $n = 1;
                    $products = [
                        'ITEM' => [],
                        'QTY' => [],
                        'AMOUNT' => [],
                    ];

                    foreach ($order->getAllVisibleItems() as $item) {
                        $products['ITEM'][$n]   = $item->getSku();
                        $products['QTY'][$n]    = round($item->getQtyOrdered());
                        $products['AMOUNT'][$n] = round($item->getPrice(), 2);
                        $n++;
                    }

                    /**
                     * Add base parameters
                     */
                    $srcParams = [
                        'PID=' . $this->getProgramId(),
                        'INT=ITEMIZED',
                        'CLICK_ID=' . $clickId,
                        http_build_query($products['ITEM'], 'ITEM'),
                        http_build_query($products['QTY'], 'QTY'),
                        http_build_query($products['AMOUNT'], 'AMOUNT'),
                        'OID=' . $order->getIncrementId(),
                    ];

                    /**
                     * Add optional parametrs
                     */
                    if ($promocode = $order->getCouponCode()) {
                        $srcParams[] = 'PROMOCODE=' . urlencode($promocode);
                    }
            }

            $html = '<div style="width:1px; height:1px; overflow:hidden; position: absolute;"><iframe src="https://t.pepperjamnetwork.com/track?'
                . implode('&', $srcParams)
                . '" width="1" height="1" frameborder="0"></iframe></div>';
        }

        return $html;
    }
}
