<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Zanox extends AbstractModel
{
    /**
     * Get applicartion id
     * @return int
     */
    public function getApplicationId($name)
    {
        $additionalData = $this->getAdditionalDataArray();
        if (isset($additionalData[$name . '_aid'])) {
            return trim($additionalData[$name . '_aid']);
        } elseif (isset($additionalData[$name])) {
            return trim($additionalData[$name]);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $html = null;
        $scheme = $this->_request->getScheme();

        if ($_section == parent::SECTION_BODYBEGIN) {

            switch (true) {
                case $this->getCplEnabled() && isset($_includeon['registration_success_pages']):
                    $currentCustommerId = null;
                    if ($currentCustommer = $this->_customerSession->getCustomer()) {
                        $currentCustommerId = $currentCustommer->getId();
                    }

                    $params = [
                        'zx_customer' => $currentCustommerId,
                        'zx_category' => 'registration_success',
                    ];

                    if ($currentCustommerId) {
                        $srcParams = 'https://ad.zanox.com/ppl/?'
                                   . $this->getApplicationId('cpl_program_code')
                                   . '&mode=[[1]]'
                                   . '&OrderID=[[' . $currentCustommerId . ']]';
                    }

                    break;
                case $this->getCpsEnabled() && isset($_includeon['checkout_success']):
                    $order = $this->getLastOrder();
                    if ($order && $order->getId()) {

                        $totalAmount = round($order->getSubtotal() - abs($order->getDiscountAmount()), 2);

                        $products = [];
                        foreach ($order->getAllVisibleItems() as $item) {
                            $products[] = [
                                'identifier'    => $item->getSku(),
                                'amount'        => (string)round($item->getPrice(), 2),
                                'currency'      => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                                'quantity'      => (string)round($item->getQtyOrdered()),
                            ];
                        }

                        $params = [
                            'zx_products'       => json_encode($products),
                            'zx_transaction'    => $order->getIncrementId(),
                            'zx_total_amount'   => (string)$totalAmount,
                            'zx_total_currency' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        ];

                        $srcParams = 'https://ad.zanox.com/pps/?' . $this->getApplicationId('cps_program_code')
                                   . '&mode=[[1]]'
                                   . '&CustomerID=[[' . ($order->getCustomerIsGuest()
                                                            ? 'guest'
                                                            : $order->getCustomerId()) . ']]'
                                   . '&OrderID=[[' . $order->getIncrementId() . ']]'
                                   . '&CurrencySymbol=[[' . $params['zx_total_currency'] . ']]'
                                   . '&TotalPrice=[[' . $params['zx_total_amount'] . ']]';
                    }
                    break;
                case $this->getCpsEnabled() && isset($_includeon['cart_page']):
                    $quote = $this->_checkoutSession->getQuote();
                    $products = [];
                    foreach ($quote->getAllVisibleItems() as $item) {
                        $products[] = [
                            'identifier'    => $item->getSku(),
                            'amount'        => (string)round($item->getPrice(), 2),
                            'currency'      => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                            'quantity'      => (string)round($item->getQty()),
                        ];
                    }

                    $params = [
                        'zx_products' => json_encode($products),
                    ];
                    break;
                case $this->getCpsEnabled() && isset($_includeon['category_page']):
                    if ($category = $this->_registry->registry('current_category')) {
                        $params = [
                            'zx_category' => $category->getName(),
                        ];
                    }
                    break;
                case $this->getCpsEnabled() && isset($_includeon['product_page']):
                    $product = $this->_registry->registry('current_product');

                    $params = [
                        'zx_identifier' => $product->getSku(),
                        'zx_fn'         => $product->getName(),
                        'zx_price'      => round($product->getFinalPrice(), 2) .' '. $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'zx_amount'     => (string)round($product->getFinalPrice(), 2),
                        'zx_currency'   => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'zx_url'        => $product->getProductUrl(),
                        'zx_photo'      => $this->_imageHelper->init($product, 'product_page_image_large')->getUrl(),
                    ];
                    break;
                default:
                    // $linkParams = $this->_getBaseParams();
                    $params = [];
                    break;
            }

            list($languageCode) = explode('_', $this->getLocaleCode());
            if ($params && $languageCode) {
                $params['zx_language'] = $languageCode;
            }

            $vars = '';
            foreach ($params as $key => $value) {
                if ($key != 'zx_products') {
                    $value = '"'. htmlspecialchars($value) .'"';
                }
                $vars .= 'var '. $key .' = '. $value .';' . "\n";
            }

            if ($vars) {
                $html = (isset($srcParams)) ? '<!-- BEGINN zanox-affiliate HTML-Code -->
<script type="text/javascript" src="' . $srcParams .'">
</script>
<noscript><img src="' . str_replace('&mode=[[1]]', '&mode=[[2]]', $srcParams) .'" width="1" height="1" /></noscript>
<!-- ENDE zanox-affiliate HTML-Code -->' : '';

                $html .= '<script type="text/javascript">'. $vars .'</script>';
            }

        } elseif ($_section == parent::SECTION_BODYEND) {
            // All pages.
            switch (true) {
                case $name = $this->getCpsEnabled() && isset($_includeon['home_page'])? 'home_page_aid' : null: break;
                case $name = $this->getCpsEnabled() && isset($_includeon['category_page'])? 'category_page_aid' : null: break;
                case $name = $this->getCpsEnabled() && isset($_includeon['product_page'])? 'product_page_aid' : null: break;
                case $name = $this->getCpsEnabled() && isset($_includeon['cart_page'])? 'cart_page_aid' : null: break;
                case $name = $this->getCpsEnabled() && isset($_includeon['one_page_chackout'])? 'cart_page_aid' : null: break;
                case $name = $this->getCpsEnabled() && isset($_includeon['checkout_success'])? 'checkout_success_aid' : null: break;
                case $name = $this->getCplEnabled() && isset($_includeon['registration_success_pages'])? 'registration_success_pages_aid' : null: break;
                case $name = $this->getCpsEnabled() && isset($_includeon['all'])? 'all_aid' : null: break;
            }

            if ($name && $aid = $this->getApplicationId($name)) {
                $html = '<div class="zx_'. htmlspecialchars($aid) .' zx_mediaslot" style="display: none;">
                            <script type="text/javascript">
                                window._zx = window._zx || [];
                                window._zx.push({"id":"'. htmlspecialchars($aid) .'"});
                                (function(d) {
                                    var s = d.createElement("script"); s.async = true;
                                        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//static.zanox.com/scripts/zanox.js";
                                    var a = d.getElementsByTagName("script")[0];
                                        a.parentNode.insertBefore(s, a);
                                } (document));
                            </script>
                        </div>';
            }
        }

        return $html;
    }
}

