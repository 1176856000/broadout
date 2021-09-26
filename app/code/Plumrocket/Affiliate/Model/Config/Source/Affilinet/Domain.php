<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Affiliate\Model\Config\Source\Affilinet;

/**
 * Integration status options.
 */
class Domain implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve parameters options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'de' => [
                'value' => 'de',
                'label' => __('Germany'),
                'script' => 'act.webmasterplan.com',
                'noscript' => 'partners.webmasterplan.com',
                'currency' => 'EUR',
            ],
            'uk' => [
                'value' => 'uk',
                'label' => __('United Kingdom'),
                'script' => 'act.successfultogether.co.uk',
                'noscript' => 'being.successfultogether.co.uk',
                'currency' => 'GBP',
            ],
            'fr' => [
                'value' => 'fr',
                'label' => __('France'),
                'script' => 'act.reussissonsensemble.fr',
                'noscript' => 'clic.reussissonsensemble.fr',
                'currency' => 'EUR',
            ],
            'nl' => [
                'value' => 'nl',
                'label' => __('Netherlands'),
                'script' => 'act.samenresultaat.nl',
                'noscript' => 'zijn.samenresultaat.nl',
                'currency' => 'EUR',
            ],
            'es' => [
                'value' => 'es',
                'label' => __('Spain'),
                'script' => 'act.epartner.es',
                'noscript' => 'web.epartner.es',
                'currency' => 'EUR',
            ],
            'ch' => [
                'value' => 'ch',
                'label' => __('Switzerland'),
                'script' => 'act.webmasterplan.com',
                'noscript' => 'partners.webmasterplan.com',
                'currency' => 'CHF',
            ],
            'at' => [
                'value' => 'at',
                'label' => __('Austria'),
                'script' => 'act.webmasterplan.com',
                'noscript' => 'partners.webmasterplan.com',
                'currency' => 'EUR',
            ],
        ];
    }

    public function getDataByCode($code)
    {
        $options = $this->toOptionArray();

        return (array_key_exists($code, $options) && is_array($options))
            ? $options[$code]
            : false;
    }
}
