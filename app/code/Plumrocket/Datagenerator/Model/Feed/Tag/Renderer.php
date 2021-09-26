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

use Magento\Framework\Escaper;
use Plumrocket\Datagenerator\Model\Feed\Cleaner;
use Plumrocket\Datagenerator\Model\Feed\TagFactory;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * @since 2.3.0
 */
class Renderer implements RendererInterface
{
    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Cleaner
     */
    private $feedTemplateCleaner;

    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Tag\Parser
     */
    private $tagParser;

    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\TagFactory
     */
    private $tagFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\Cleaner    $feedTemplateCleaner
     * @param \Plumrocket\Datagenerator\Model\Feed\Tag\Parser $tagParser
     * @param \Plumrocket\Datagenerator\Model\Feed\TagFactory $tagFactory
     * @param \Magento\Framework\Escaper                      $escaper
     */
    public function __construct(
        Cleaner $feedTemplateCleaner,
        Parser $tagParser,
        TagFactory $tagFactory,
        Escaper $escaper
    ) {
        $this->feedTemplateCleaner = $feedTemplateCleaner;
        $this->tagParser = $tagParser;
        $this->tagFactory = $tagFactory;
        $this->escaper = $escaper;
    }

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface $tag
     * @param mixed                                             $propertyValue
     * @param string                                            $feedTemplatePart
     * @param string                                            $extension
     * @return string
     */
    public function render(TagInterface $tag, $propertyValue, string $feedTemplatePart, string $extension): string
    {
        if ($tag->isPaired()) {
            return $this->renderPairedTag($tag, $propertyValue, $feedTemplatePart, $extension);
        }

        return $this->renderSingularTag($tag, $propertyValue, $feedTemplatePart, $extension);
    }

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface $tag
     * @param mixed                                             $propertyValue
     * @param string                                            $feedTemplatePart
     * @param string                                            $extension
     * @return string
     */
    private function renderSingularTag(
        TagInterface $tag,
        $propertyValue,
        string $feedTemplatePart,
        string $extension
    ): string {
        $propertyValue = $this->feedTemplateCleaner->cleanValue($propertyValue, $extension);

        switch ($extension) {
            case 'csv':
                $propertyValue = $this->escapeForCsv($propertyValue);
                break;
            case 'xml':
                $propertyValue = $this->escapeForXml($tag, $propertyValue);
                break;
        }

        return str_replace("{{$tag->getCode()}}", $propertyValue, $feedTemplatePart);
    }

    /**
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface $tag
     * @param                                                   $propertyValue
     * @param string                                            $feedTemplatePart
     * @param string                                            $extension
     * @return string
     */
    private function renderPairedTag(
        TagInterface $tag,
        $propertyValue,
        string $feedTemplatePart,
        string $extension
    ): string {
        $tagContent = '';

        $openedCode = $this->escapeForRegex($tag->getCode());
        $closedCode = $this->escapeForRegex("{$tag->getEntityType()}.{$tag->getPropertyName()}");

        $regexToSearch = "#{{$openedCode}}([\s\S]*?){\/{$closedCode}}#m";
        $regexToReplace = "#{{$openedCode}}[\s\S]*?{\/{$closedCode}}#m";
        if (preg_match($regexToSearch, $feedTemplatePart, $matches)) {
            $lineTemplate = trim($matches[1]);

            $repeatedEntity = '';
            foreach ($tag->getModifiers() as $modifierName => $modifierParamsString) {
                if ('repeat' === $modifierName) {
                    $repeatedEntity = $modifierParamsString;
                }
            }

            $repeatedTags = [];
            foreach ($this->tagParser->parse($lineTemplate) as $tagData) {
                $repeatedTag = $this->tagFactory->create($tagData);
                if ($repeatedEntity === $repeatedTag->getEntityType()) {
                    $repeatedTags[] = $repeatedTag;
                }
            }

            foreach ($propertyValue as $value) {
                $lineText = $lineTemplate;
                foreach ($repeatedTags as $repeatedTag) {
                    $lineText = $this->render(
                        $repeatedTag,
                        $value[$repeatedTag->getPropertyName()] ?? '',
                        $lineText,
                        $extension
                    );
                }
                $tagContent .= "\n" . $lineText;
            }
        }

        return preg_replace($regexToReplace, $tagContent, $feedTemplatePart);
    }

    /**
     * @param string $string
     * @return string
     */
    private function escapeForRegex(string $string): string
    {
        return str_replace(['.', '|', '-'], ['\.', '\|', '\-'], $string);
    }

    /**
     * Escape tag value for xml
     *
     * @param \Plumrocket\Datagenerator\Model\Feed\TagInterface $tag
     * @param string                                            $propertyValue
     * @return string
     */
    private function escapeForXml(TagInterface $tag, string $propertyValue): string
    {
        $modifiers = $tag->getModifiers();
        if (array_key_exists('attrib', $modifiers) && 'yes' === $modifiers['attrib']) {
            return $this->escaper->escapeHtmlAttr($propertyValue, false);
        }

        if ((strpos($propertyValue, '<') !== false)
            || (strpos($propertyValue, '>') !== false)
            || (strpos($propertyValue, '&') !== false)
        ) {
            $propertyValue = str_replace(['<![CDATA[', ']]>'], '', $propertyValue);
            $propertyValue = "<![CDATA[$propertyValue]]>";
        }
        return $propertyValue;
    }

    /**
     * @param string $propertyValue
     * @return string
     */
    private function escapeForCsv(string $propertyValue): string
    {
        if ($propertyValue) {
            $propertyValue = str_replace('"', '""', $propertyValue);
            if (! preg_match('/^[0-9.]+$/', $propertyValue)) {
                $propertyValue = '"' . $propertyValue . '"';
            }
        }
        return $propertyValue;
    }
}
