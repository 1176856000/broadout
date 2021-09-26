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
 * Gets tags and theirs modifiers from feed template
 *
 * @since 2.3.0
 */
class Parser
{
    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Tag\Modifier\Parser
     */
    private $modifierParser;

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\Tag\Modifier\Parser $modifierParser
     */
    public function __construct(Modifier\Parser $modifierParser)
    {
        $this->modifierParser = $modifierParser;
    }

    /**
     * @param string $template
     * @return array[]
     */
    public function parse(string $template): array
    {
        $tags = [];
        if (preg_match_all('#{([^.}/]+\.[^}]+)}#', $template, $matches)) {
            foreach ($matches[1] as $tagString) {
                $parts = explode('|', $tagString);
                $entityAndAttr = array_shift($parts);
                list($entityType, $propertyName) = explode('.', $entityAndAttr);
                $tagCode = "$entityType.$propertyName";
                $isPaired = strpos($template, "{/$tagCode}") !== false;

                $tags[$tagString] = [
                    TagInterface::CODE          => $tagString,
                    TagInterface::ENTITY_TYPE   => $entityType,
                    TagInterface::PROPERTY_NAME => $propertyName,
                    TagInterface::MODIFIERS     => $this->modifierParser->parse($parts),
                    TagInterface::IS_PAIRED     => $isPaired
                ];
            }
        }

        return $tags;
    }
}
