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

namespace Plumrocket\Datagenerator\Model\Feed\Tag;

use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * Use specified in tag modifiers to received value
 *
 * @since 2.3.0
 */
class ModifierComposite implements ModifierInterface
{
    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface[]
     */
    private $modifiers;

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\Tag\ModifierInterface[] $modifiers
     */
    public function __construct(array $modifiers = [])
    {
        $this->modifiers = $modifiers;
    }

    /**
     * @inheritDoc
     */
    public function modify(TagInterface $tag, $propertyValue, string $paramsString = '', $entityObject = null)
    {
        foreach ($tag->getModifiers() as $modifierName => $modifierParamsString) {
            if (isset($this->modifiers[$modifierName])) {
                $propertyValue = $this->modifiers[$modifierName]->modify(
                    $tag,
                    $propertyValue,
                    $modifierParamsString,
                    $entityObject
                );
            }
        }
        return $propertyValue;
    }
}
