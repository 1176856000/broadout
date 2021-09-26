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

namespace Plumrocket\Datagenerator\Model\Feed\Tag\Modifier;

use Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Cut string by gave length and add specific symbols (or ...) to the end of the string
 *
 * Format: "|truncate:{length}:[{string for end}]"
 * For example: "|truncate:500:..."
 *
 * @since 2.3.0
 */
class MaxStringLengthModifier implements ModifierInterface
{
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context
    ) {
        $this->filterManager = $context->getFilterManager();
    }

    /**
     * @inheritDoc
     */
    public function modify(TagInterface $tag, $propertyValue, string $paramsString = '', $entityObject = null)
    {
        $params = explode(':', $paramsString);
        $length = (int) $params[0];
        $end = $params[1] ?? '...';

        return $this->filterManager->truncate($propertyValue, ['length' => $length, 'etc' => $end]);
    }
}
