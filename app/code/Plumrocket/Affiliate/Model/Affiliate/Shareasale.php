<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Shareasale extends AbstractModel
{
    const DEFAULT_STORE_ID = 1;

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        return $this->_getRenderedCode($_section, $_includeon);
    }

    public function getStoreid()
    {
        return $this->getAdditionalDataValue('storeid', self::DEFAULT_STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getRenderedCode($_section, $_includeon = null)
    {
        $params = [];

        $sscidmode = 6;
        $sscid = $this->_cookieManager->getCookie('shareasaleSSCID') ?: '';

        switch ($_section) {
            case parent::SECTION_HEAD:

                $code = '<script type="text/javascript">
                    var shareasaleSSCID = shareasaleGetParameterByName("sscid");

                    function shareasaleSetCookie(e, a, r, s, t) {
                        if (e && a) {
                            var o, n = s ? "; path=" + s : "",
                                i = t ? "; domain=" + t : "",
                                l = "";
                            r && ((o = new Date).setTime(o.getTime() + r), l = "; expires=" + o.toUTCString()), document.cookie = e + "=" + a + l + n + i
                        }
                    }

                    function shareasaleGetParameterByName(e, a) {
                        a || (a = window.location.href), e = e.replace(/[\[\]]/g, "\\$&");
                        var r = new RegExp("[?&]" + e + "(=([^&#]*)|&|#|$)").exec(a);
                        return r ? r[2] ? decodeURIComponent(r[2].replace(/\+/g, " ")) : "" : null
                    }
                    shareasaleSSCID && shareasaleSetCookie("shareasaleSSCID", shareasaleSSCID, 94670778e4, "/");
                </script>';
                break;

            case parent::SECTION_BODYBEGIN:
                $order = $this->getLastOrder();
                if ($order && $order->getId()) {

                    $orderedItems = $order->getAllVisibleItems();
                    $skulist = ''; //setup empty skulist param
                    $pricelist = ''; //setup empty pricelist param
                    $quantitylist = ''; //setup empty quantitylist param

                    $lastIndex = array_search(end($orderedItems), $orderedItems, true);
                    foreach ($orderedItems as $index => $item) {
                        $delimiter = $index === $lastIndex ? '' : ',';
                        $skulist .= $item->getSku() . $delimiter;
                        $quantitylist .= ceil($item->getQtyOrdered()) . $delimiter;
                        //append correct item base price, before any kind of cart or product discount
                        $pricelist .= ($item->getProduct()->getFinalPrice() - ($item->getDiscountAmount() / $item->getQtyOrdered())) . $delimiter;
                    }

                    $params = [
                        'tracking'      => $order->getIncrementId(),
                        'amount'        => $order->getSubtotal() + $order->getDiscountAmount(),
                        'transtype'     => 'sale',
                        'merchantID'    => $this->getMerchantId(),
                        'couponcode'    => $order->getCouponCode(),
                        'skulist'       => $skulist,
                        'quantitylist'  => $quantitylist,
                        'pricelist'     => $pricelist,
                        'newcustomer'   => $this->isNewCustomer($order),
                        'currency'      => $order->getOrderCurrencyCode(),
                        'storeid'       => $this->getStoreid(),
                        'sscid'         => $sscid,
                        'sscidmode'     => $sscidmode,
                    ];
                }

                $code = $this->createImagePixelHtml($params);
                break;
            case parent::SECTION_BODYEND:
                $customer = $this->_customerSession->getCustomer();
                if ($customer && $customer->getId()) {
                    $params = [
                        'amount'        => '0.00',
                        'tracking'      => ($customer) ? $customer->getEmail() : '',
                        'merchantID'    => $this->getMerchantId(),
                        'transtype'     => 'lead',
                        'sscid'         => $sscid,
                        'sscidmode'     => $sscidmode,
                    ];
                }

                $code = $this->createImagePixelHtml($params);
                break;
            default:
                $code = '';
        }

        return $code;
    }

    /**
     * @param $value
     * @return string
     */
    private function createImagePixelHtml($value)
    {
        return '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                  <img src="https://shareasale.com/sale.cfm?' . http_build_query($value) . '" width="1" height="1" />
            </div>';
    }
}
