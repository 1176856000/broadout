<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class AffiliateFuture extends AbstractModel
{

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $merchantID = $this->getMerchantId();

        $html = '<script src="//tags.affiliatefuture.com/' . $merchantID . '.js"></script>';

        $prepareHtml = null;

        if ($_section == parent::SECTION_BODYBEGIN) {
            if (isset($_includeon['checkout_success']) && $this->getCpsEnabled()) {
                /**
                 * @var \Magento\Sales\Model\Order $order
                 */
                $order = $this->getLastOrder();
                if ($order && $order->getId()) {

                    $orderRef = $order->getIncrementId();;
                    $orderValue = round($order->getGrandTotal(), 2);
                    $payoutCodes = '';
                    $offlineCode = '';

                    $prepareHtml .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">';
                    $prepareHtml .= '<img src="//scripts.affiliatefuture.com/AFSaleV5.asp?orderID='.$orderRef.'&orderValue='.$orderValue.'&merchant='.$merchantID.'&payoutCodes=&offlineCode=&voucher=&products=&curr=&r=&img=yes" />';
                    $prepareHtml .= '<script language="javascript">
                                (function(){try{
                                    var merchantID = "'.$merchantID.'";
                                    var orderValue = "'.$orderValue.'";
                                    var orderRef = "'.$orderRef.'";
                                    var payoutCodes = "";
                                    var offlineCode = "";
                                    var voucher = "";
                                    var products = "";
                                    var curr = "";

                                    AFProcessSaleV5(merchantID, orderValue, orderRef,payoutCodes,offlineCode, voucher, products, curr);
                                }catch(e){window.console && window.console.log(e)}}());
                            </script>';
                    $prepareHtml .= '</div>';
                }
            }
        } elseif ($_section == parent::SECTION_BODYEND) {
            if (isset($_includeon['registration_success_pages']) && $this->getCplEnabled()) {
                $customer = $this->_customerSession->getCustomer();
                if ($customer && $customer->getId()) {

                    $ref = $customer->getId();
                    $payoutCodes = '';
                    $offlineCode = '';

                    $prepareHtml .= '<div style="width:1px; height:1px; overflow:hidden; position: absolute;">';
                    $prepareHtml .= '<img src="//scripts.affiliatefuture.com/AFLeadV5.asp?ref='.$ref.'&merchant='.$merchantID.'&payoutCodes=&img=yes" />';
                    $prepareHtml .= '<script language="javascript">
                                (function(){try{
                                    var merchantID = "'.$merchantID.'";
                                    var ref = "'.$ref.'";
                                    var payoutCodes = "";
                                    var offlineCode = "";
                                    var voucher = "";
                                    var products = "";
                                    var curr = "";

                                    AFProcessLeadV5(merchantID, payoutCodes, offlineCode, ref, voucher, products, curr);
                                }catch(e){window.console && window.console.log(e)}}());
                            </script>';
                    $prepareHtml .= '</div>';
                }
            }
        }

        if ($prepareHtml) {
            $html .= '<script language="javascript" src="//scripts.affiliatefuture.com/AFFunctions.js"></script>' . $prepareHtml;
        }

        return $html;
    }
}
