<?php
/**
 * @package     Plumrocket_Affiliate
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://www.plumrocket.com)
 * @license     https://www.plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Affiliate\Model\Affiliate\Pixel;

/**
 * @since 2.8.0
 */
class JsRenderer
{
    /**
     * Wrap js code with html tag
     *
     * @param string $jsCode
     * @return string
     */
    public function wrapJsCode(string $jsCode): string
    {
        return '<script>' . $jsCode . '</script>';
    }
}
