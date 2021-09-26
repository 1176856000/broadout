<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Affiliate;

class Hasoffers extends AbstractModel
{

    const TYPE_ID = 3;
    const STORAGE_NAME = 'praf_hodata';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager             $cookieManager
     * @param \Plumrocket\Affiliate\Helper\Data                             $dataHelper
     * @param \Magento\Framework\Model\Context                              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Customer\Model\Session                               $customerSession
     * @param \Magento\Checkout\Model\Session                               $checkoutSession
     * @param \Magento\Framework\App\RequestInterface                       $request
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Catalog\Model\ProductFactory                         $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory                        $categoryFactory
     * @param \Magento\Sales\Model\OrderFactory                             $orderFactory
     * @param \Magento\Directory\Model\RegionFactory                        $regionFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress          $remoteAddress
     * @param \Magento\Catalog\Helper\Image                                 $imageHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface            $scopeConfigInterface
     * @param \Magento\Framework\Url                                        $url
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                   $dateTime
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null  $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null            $resourceCollection
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Url $url,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dateTime = $dateTime;

        parent::__construct(
            $cookieManager,
            $dataHelper,
            $context,
            $registry,
            $customerSession,
            $checkoutSession,
            $request,
            $storeManager,
            $productFactory,
            $categoryFactory,
            $orderFactory,
            $regionFactory,
            $remoteAddress,
            $imageHelper,
            $scopeConfigInterface,
            $url,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Get default additonal data
     * @return array
     */
    public function getDefaultAdditionalDataArray(): array
    {
        return [
            'postback_params'   => [
                'advertiser_id' => ['key' => 'advertiser_id', 'value' => 'advertiser_id', 'is_editable' => 1, 'description' => 'ID of advertiser.'],
                'advertiser_ref' => ['key' => 'advertiser_ref', 'value' => 'advertiser_ref', 'is_editable' => 1, 'description' => 'Reference ID for affiliate.'],
                'adv_sub' => ['key' => 'adv_sub', 'value' => 'adv_sub', 'is_editable' => 1, 'description' => 'Advertiser sub specified in the conversion pixel / URL.'],
                'aff_sub' => ['key' => 'aff_sub', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub specified in the tracking link.'],
                'aff_sub2' => ['key' => 'aff_sub2', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 2 specified in the tracking link.'],
                'aff_sub3' => ['key' => 'aff_sub3', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 3 specified in the tracking link.'],
                'aff_sub4' => ['key' => 'aff_sub4', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 4 specified in the tracking link.'],
                'aff_sub5' => ['key' => 'aff_sub5', 'value' => 'aff_sub', 'is_editable' => 1, 'description' => 'Affiliate sub 5 specified in the tracking link.'],
                'affiliate_id' => ['key' => 'affiliate_id', 'value' => 'affiliate_id', 'is_editable' => 1, 'description' => 'ID of affiliate.'],
                'affiliate_name' => ['key' => 'affiliate_name', 'value' => 'affiliate_name', 'is_editable' => 1, 'description' => 'Company name of affiliate.'],
                'affiliate_ref' => ['key' => 'affiliate_ref', 'value' => 'affiliate_ref', 'is_editable' => 1, 'description' => 'Reference ID for affiliate.'],
                'currency' => ['key' => 'currency', 'value' => 'currency', 'is_editable' => 0, 'description' => '3 digit currency abbreviated.'],
                'order_id' => ['key' => 'order_id', 'value' => 'order_id', 'is_editable' => 0, 'description' => 'Order ID.'],
                'date' => ['key' => 'date', 'value' => 'date', 'is_editable' => 0, 'description' => 'Current date of conversion formatted as YYYY-MM-DD.'],
                'datetime' => ['key' => 'datetime', 'value' => 'datetime', 'is_editable' => 0, 'description' => 'Current date and time of conversion formatted as YYYY-MM-DD HH:MM:SS.'],
                'device_id' => ['key' => 'device_id', 'value' => 'device_id', 'is_editable' => 1, 'description' => 'For mobile app tracking, the ID of the user\'s mobile device.'],
                'file_name' => ['key' => 'file_name', 'value' => 'file_name', 'is_editable' => 1, 'description' => 'Name of creative file for offer.'],
                'goal_id' => ['key' => 'goal_id', 'value' => 'goal_id', 'is_editable' => 1, 'description' => 'ID of goal for offer.'],
                'ip' => ['key' => 'ip', 'value' => 'ip', 'is_editable' => 0, 'description' => 'IP address that made the conversion request.'],
                'payout' => ['key' => 'payout', 'value' => 'payout', 'is_editable' => 1, 'description' => 'Amount paid to affiliate for conversion.'],
                'offer_file_id' => ['key' => 'offer_file_id', 'value' => 'offer_file_id', 'is_editable' => 1, 'description' => 'ID of creative file for offer.'],
                'offer_id' => ['key' => 'offer_id', 'value' => 'offer_id', 'is_editable' => 1, 'description' => 'ID of offer.'],
                'offer_name' => ['key' => 'offer_name', 'value' => 'offer_name', 'is_editable' => 1, 'description' => 'Name of offer.'],
                'offer_ref' => ['key' => 'offer_ref', 'value' => 'offer_ref', 'is_editable' => 1, 'description' => 'Reference ID for offer.'],
                'offer_url_id' => ['key' => 'offer_url_id', 'value' => 'offer_url_id', 'is_editable' => 1, 'description' => 'ID of offer URL for offer.'],
                'ran' => ['key' => 'ran', 'value' => 'ran', 'is_editable' => 1, 'description' => 'Randomly generated number.'],
                'amount' => ['key' => 'amount', 'value' => 'amount', 'is_editable' => 0, 'description' => 'Sale amount generated for advertiser from conversion.'],
                'sale_amount' => ['key' => 'sale_amount', 'value' => 'sale_amount', 'is_editable' => 0, 'description' => 'Sale amount generated for advertiser from conversion.'],
                'session_ip' => ['key' => 'session_ip', 'value' => 'session_ip', 'is_editable' => 1, 'description' => 'IP address that started the tracking session.'],
                'source' => ['key' => 'source', 'value' => 'source', 'is_editable' => 1, 'description' => 'Source value specified in the tracking link.'],
                'time' => ['key' => 'time', 'value' => 'time', 'is_editable' => 0, 'description' => 'Current time of conversion formatted as HH:MM:SS.'],
                'transaction_id' => ['key' => 'transaction_id', 'value' => 'transaction_id', 'is_editable' => 1, 'description' => 'ID of the transaction for your network. Don\'t get confused with an ID an affiliate passes into aff_sub.'],
                'delivery_cost_inclusive' => ['key' => 'delivery_cost_inclusive', 'value' => 'delivery_cost_inclusive'],
                'taxes_inclusive' => ['key' => 'taxes_inclusive', 'value' => 'taxes_inclusive'],
            ],
        ];
    }

    /**
     * Set additional data values
     * @param array $values
     * @return  $this
     */
    public function setAdditionalDataValues($values)
    {
        $data = $this->getAdditionalDataArray();

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $data[$key][$k]['value'] = $v;
                }
            }
        }

        $this->setAdditionalData(json_encode($data));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeHtml($_section, $_includeon = null)
    {
        $getSectionCode = 'getSection'.ucfirst($_section).'Code';
        if ($code = $this->$getSectionCode()) {
            if (strip_tags($code) != $code) {
                $code = $code."\n\r";
            } else {
                $code = '
                    <div style="width:1px; height:1px; overflow:hidden; position: absolute;">
                        <img src="'.$code.'" width="1" height="1" />
                    </div>';
            }
            $code = $this->_renderCode($code);
        }
        return $code;
    }

    /**
     * Render code
     * @param  string $code
     * @return string
     */
    protected function _renderCode($code)
    {
        foreach ($this->getDinamicHasoffersPostbackData() as $key => $value) {
            $code = str_replace('{'.$key.'}', $value, $code);
        }

        $rData = $this->getPlumrocketAffiliateHasoffersRequestData();
        $aData = $this->getAdditionalDataArray();

        foreach ($aData['postback_params'] as $key => $param) {
            if (isset($rData[$param['value']])) {
                $value = $rData[$param['value']];
                $code = str_replace('{'.$key.'}', $value, $code);
            }
        }

        foreach ($aData['postback_params'] as $param) {
            if (strpos($code, '{'.$param['value'].'}') !== false) {
                return '<!-- affiliate log: can not parse '.'{'.$param['value'].'}'.' -->';
            }
        }

        return $code;
    }

    /**
     * Get dinamic hasoffers postback data
     * @return array
     */
    public function getDinamicHasoffersPostbackData()
    {
        $data = [];

        $data['currency']   = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $data['data']       = $this->dateTime->date('Y-m-d');
        $data['datetime']   = $this->dateTime->date('Y-m-d H:i:s');
        $data['time']       = $this->dateTime->date('H:i:s');
        $data['ip']         = $this->_remoteAddress->getRemoteAddress(true);

        if ($order = $this->getLastOrder()) {
            $settings = $this->getAdditionalDataArray();
            $totalPrice = 0;

            $includeTaxes = $settings['postback_params']['taxes_inclusive']['value'] ?? false;
            if ($includeTaxes) {
                foreach ($order->getAllVisibleItems() as $item) {
                    $price = $item->getPriceInclTax() * $item->getQtyOrdered();
                    $price -= $item->getDiscountAmount();
                    $totalPrice += $price;
                }
            } else {
                $totalPrice = $order->getSubtotal();
            }

            $includeDeliveryCost = $settings['postback_params']['delivery_cost_inclusive']['value'] ?? false;
            if ($includeDeliveryCost) {
                $totalPrice += $order->getShippingInclTax();
            }

            $data['sale_amount'] = $data['amount'] = $totalPrice;
            $data['order_id'] = $order->getIncrementId();
        }

        return $data;
    }

    /**
     * set request data in session
     * @return $this
     */
    public function getPlumrocketAffiliateHasoffersRequestData()
    {
        $data = json_decode($this->_cookieManager->getCookie(self::STORAGE_NAME), true);
        if (!is_array($data)) {
            $data = [];
        }
        return $data;
    }
}
