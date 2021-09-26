<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface;
use Plumrocket\Affiliate\Model\Affiliate\CommissionJunction;
use Plumrocket\Affiliate\Model\Affiliate\Hasoffers;
use Plumrocket\Affiliate\Model\Affiliate\Pepperjam;
use Plumrocket\Affiliate\Model\Affiliate\Tradedoubler;

class Onload extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\Affiliate\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieManager;

    /**
     * @var \Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface
     */
    private $affiliateProgramProvider;

    /**
     * @param \Magento\Framework\View\Element\Template\Context            $context
     * @param \Plumrocket\Affiliate\Helper\Data                           $dataHelper
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager           $cookieManager
     * @param \Plumrocket\Affiliate\Api\AffiliateProgramProviderInterface $affiliateProgramProvider
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Affiliate\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager,
        AffiliateProgramProviderInterface $affiliateProgramProvider,
        array $data = []
    ) {
        $this->cookieManager = $cookieManager;
        $this->dataHelper = $dataHelper;
        $this->affiliateProgramProvider = $affiliateProgramProvider;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return '';
        }

        $done = [];

        $jsCode = '';
        foreach ($this->affiliateProgramProvider->get() as $affiliate) {
            if (in_array($affiliate->getTypeId(), $done, true)) {
                continue;
            }

            $done[] = $affiliate->getTypeId();
            $request = $this->getRequest();

            switch ($affiliate->getTypeId()) {
                case Tradedoubler::TYPE_ID:
                    if ($tduid = $request->getParam('tduid')) {
                        $jsCode .= "$.cookie('" . (Tradedoubler::STORAGE_NAME)
                            . "', '{$this->escapeHtml($tduid)}');";
                    }
                    break;
                case Hasoffers::TYPE_ID:
                    $params = $request->getParams();
                    $additionalData = $affiliate->getAdditionalDataArray();
                    $jsCode .= "
                        var hoData = JSON.parse($.cookie('" . (Hasoffers::STORAGE_NAME) . "'));
                        if (!hoData) hoData = {};";
                    $changed = false;

                    foreach ($additionalData['postback_params'] as $item) {
                        $key = $item['value'];

                        if (! empty($params[$key])) {
                            $jsCode .= "hoData['{$key}'] = '{$params[$key]}';";
                            $changed = true;
                        }
                    }

                    if ($changed) {
                        $jsCode .= "$.cookie('" . (Hasoffers::STORAGE_NAME) . "', hoData);";
                    }
                    break;

                case CommissionJunction::TYPE_ID:
                    if ($cjevent = $request->getParam('cjevent')) {
                        $jsCode .= "$.cookie('" . (CommissionJunction::STORAGE_NAME)
                            . "', '{$this->escapeHtml($cjevent)}', { expires: 60, path: '/' });";
                    }
                    break;

                case Pepperjam::TYPE_ID:
                    if ($clickId = $request->getParam('clickId')) {
                        $cookieData = $this->cookieManager->getCookie(Pepperjam::STORAGE_NAME);
                        $jsonSerializer = ObjectManager::getInstance()->get(Json::class);
                        $cookieData = ! $cookieData ? [] : $jsonSerializer->unserialize($cookieData);

                        if (! in_array($clickId, $cookieData)) {
                            $cookieData[] = $clickId;
                            $encodedData = $jsonSerializer->serialize($cookieData);
                            $jsCode .= "$.cookie('" . (Pepperjam::STORAGE_NAME) . "', '{$encodedData}');";
                        }
                    }
                    break;
            }
        }

        if (! $jsCode) {
            return '';
        }

        return "
        <script>
            require(['jquery', 'jquery/jquery.cookie'], function ($) {
                'use strict';
                $jsCode
            });
        </script>";
    }
}
