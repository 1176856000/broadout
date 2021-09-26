<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Datagenerator
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Datagenerator\Model\Config\Source\GoogleShopping;

class Language extends AbstractGoogleShopping
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        foreach ($this->toArray() as $code => $language) {
            $options[] = [
                'label' => __($language),
                'value' => $code,
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        foreach ($this->countries as $country => $languages) {
            foreach ($languages as $code => $label) {
                $options[$code] = $label;
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        foreach ($this->countries as $country => $languages) {
            foreach ($languages as $code => $label) {
                $options[$country] = [
                    'label' => (string) __($label),
                    'value' => $languages,
                ];
            }
        }

        return $options;
    }
}
