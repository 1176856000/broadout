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

namespace Plumrocket\Datagenerator\Model\Feed;

/**
 * Performs searching and filtering by tags like: {no_br}, {no_html}, etc.
 *
 * @since 2.3.0
 */
class Cleaner
{
    /**
     * @param        $propertyValue
     * @param string $extension
     * @return string
     */
    public function cleanValue($propertyValue, string $extension): string
    {
        // cannot render nested array
        if (is_array($propertyValue)
            && count($propertyValue)
            && is_array($propertyValue[array_keys($propertyValue)[0]])
        ) {
            return '';
        }

        if (is_array($propertyValue)) {
            $noArray = implode(',', $propertyValue);
        } else {
            $noArray = (string) $propertyValue;
        }

        return $this->cleanTemplate($noArray, $extension);
    }

    /**
     * @param string $feedTemplate
     * @param string       $extension
     * @return string
     */
    public function cleanTemplate(string $feedTemplate, string $extension): string
    {
        preg_match_all(
            '/[\s]*\{no_(br|html|quotes|br_html)\}(.*)\{\/no_(br|html|quotes|br_html)\}[\s]*/smU',
            $feedTemplate,
            $nodes,
            PREG_PATTERN_ORDER
        );

        if ($nodes[1]) {
            foreach ($nodes[1] as $key => $no_item) {
                $nodeText = '';
                switch ($no_item) {
                    case 'br_html':
                        $nodeText = rtrim(
                            str_replace(["\r", "\n"], ' ', $nodes[2][$key])
                        );
                        $nodeText = strip_tags($nodeText);
                        if (!empty($nodeText) && 'xml' === $extension) {
                            $nodeText = '<![CDATA[' . $nodeText;
                        }
                        break;
                    case 'br':
                        $nodeText = rtrim(
                            str_replace(["\r", "\n"], ' ', $nodes[2][$key])
                        );
                        break;
                    case 'html':
                        $nodeText = strip_tags($nodes[2][$key]);

                        if ('xml' === $extension) {
                            $nodeText = '<![CDATA[' . $nodeText;
                        }
                        break;
                    case 'quotes':
                        $nodeText = str_replace('"', '', $nodes[2][$key]);
                        break;
                }
                if ($feedTemplate) {
                    $feedTemplate = str_replace($nodes[0][$key], $nodeText, $feedTemplate);
                }
            }
        }
        return preg_replace('/\{(product|category|site|child|no_)\.[a-z0-9\_]+\}/', '', $feedTemplate);
    }
}
