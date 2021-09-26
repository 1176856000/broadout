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

use Magento\Framework\Data\OptionSourceInterface;

abstract class AbstractGoogleShopping implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $countries = [
        'Argentina' => [
            'es-ES' => 'Spanish'
        ],
        'Australia' => [
            'en-AU' => 'English'
        ],
        'Austria' => [
            'de-DE' => 'German'
        ],
        'Belgium' => [
            'fr-FR' => 'French',
            'nl-NL' => 'Dutch'
        ],
        'Brazil' => [
            'pt-BR' => 'Portuguese'
        ],
        'Canada' => [
            'en-US' => 'English',
            'fr-FR' => 'French'
        ],
        'Chile' => [
            'es-ES' => 'Spanish'
        ],
        'Colombia' => [
            'es-ES' => 'Spanish'
        ],
        'Czechia' => [
            'cs-CZ' => 'Czech'
        ],
        'Denmark' => [
            'da-DK' => 'Danish'
        ],
        'France' => [
            'fr-FR' => 'French'
        ],
        'Germany' => [
            'de-DE' => 'German'
        ],
        'Hong Kong' => [
            'en-US' => 'English'
        ],
        'India' => [
            'en-US' => 'English'
        ],
        'Indonesia' => [
            'en-US' => 'English'
        ],
        'Ireland' => [
            'en-GB' => 'English'
        ],
        'Israel' => [
            'en-US' => 'English'
        ],
        'Italy' => [
            'it-IT' => 'Italian'
        ],
        'Japan' => [
            'ja-JP' => 'Japanese'
        ],
        'Malaysia' => [
            'en-US' => 'English'
        ],
        'Mexico' => [
            'en-US' => 'English'
        ],
        'Netherlands' => [
            'nl-NL' => 'Dutch'
        ],
        'New Zealand' => [
            'en-AU' => 'English'
        ],
        'Norway' => [
            'en-US' => 'English'
        ],
        'Philippines' => [
            'en-US' => 'English'
        ],
        'Poland' => [
            'pl-PL' => 'Polish'
        ],
        'Portugal' => [
            'pt-BR' => 'Portuguese'
        ],
        'Russia' => [
            'ru-RU' => 'Russian'
        ],
        'South Africa' => [
            'en-US' => 'English'
        ],
        'South Korea' => [
            'en-US' => 'English'
        ],
        'Spain' => [
            'es-ES' => 'Spanish'
        ],
        'Sweden' => [
            'sv-SE' => 'Swedish'
        ],
        'Switzerland' => [
            'en-US' => 'English',
            'fr-CH' => 'French',
            'de-CH' => 'German',
            'it-CH' => 'Italian'
        ],
        'Taiwan' => [
            'en-US' => 'English'
        ],
        'Turkey' => [
            'tr-TR' => 'Turkish'
        ],
        'United Arab Emirates' => [
            'en-US' => 'English'
        ],
        'United Kingdom' => [
            'en-GB' => 'English'
        ],
        'United States' => [
            'en-US' => 'English'
        ]
    ];
}
