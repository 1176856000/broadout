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

namespace Plumrocket\Datagenerator\Test\Unit\Model\Feed\Tag;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\Datagenerator\Model\Feed\Tag\Parser;
use Plumrocket\Datagenerator\Model\Feed\TagInterface;

/**
 * @since 1.0.0
 */
class ParserTest extends TestCase
{
    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Tag\Parser
     */
    private $parser;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $modifierParser = $objectManager->getObject(
            \Plumrocket\Datagenerator\Model\Feed\Tag\Modifier\Parser::class
        );
        $this->parser = $objectManager->getObject(Parser::class, ['modifierParser' => $modifierParser]);
    }

    /**
     * @dataProvider templates
     * @param string $template
     * @param array  $expect
     */
    public function testParseTemplate(string $template, array $expect): void
    {
        self::assertSame($expect, $this->parser->parse($template));
    }

    public function templates(): \Generator
    {
        yield [
            'template' => '',
            'expect' => [],
        ];
        yield [
            'template' => '{product.name}',
            'expect'   => [
                'product.name' => [
                    TagInterface::CODE          => 'product.name',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'name',
                    TagInterface::MODIFIERS     => [],
                    TagInterface::IS_PAIRED     => false,
                ],
            ],
        ];
        yield [
            'template' => '{product.image_url}',
            'expect'   => [
                'product.image_url' => [
                    TagInterface::CODE          => 'product.image_url',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'image_url',
                    TagInterface::MODIFIERS     => [],
                    TagInterface::IS_PAIRED     => false,
                ],
            ],
        ];
        yield [
            'template' => '{product.image_url|size:150}',
            'expect'   => [
                'product.image_url|size:150' => [
                    TagInterface::CODE          => 'product.image_url|size:150',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'image_url',
                    TagInterface::MODIFIERS     => ['size' => '150'],
                    TagInterface::IS_PAIRED     => false,
                ],
            ],
        ];
        yield [
            'template' => '{product.name}{product.image_url|size:150}',
            'expect'   => [
                'product.name' => [
                    TagInterface::CODE          => 'product.name',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'name',
                    TagInterface::MODIFIERS     => [],
                    TagInterface::IS_PAIRED     => false,
                ],
                'product.image_url|size:150' => [
                    TagInterface::CODE          => 'product.image_url|size:150',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'image_url',
                    TagInterface::MODIFIERS     => ['size' => '150'],
                    TagInterface::IS_PAIRED     => false,
                ],
            ],
        ];
        yield [
            'template' => '{product.additional_images}<img src="{additional_image.url}">{/product.additional_images}',
            'expect'   => [
                'product.additional_images' => [
                    TagInterface::CODE          => 'product.additional_images',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'additional_images',
                    TagInterface::MODIFIERS     => [],
                    TagInterface::IS_PAIRED     => true,
                ],
                'additional_image.url' => [
                    TagInterface::CODE          => 'additional_image.url',
                    TagInterface::ENTITY_TYPE   => 'additional_image',
                    TagInterface::PROPERTY_NAME => 'url',
                    TagInterface::MODIFIERS     => [],
                    TagInterface::IS_PAIRED     => false,
                ],
            ],
        ];
        yield [
            'template' => '{product.additional_images|repeat:additional_image}
                <img src="{additional_image.url}">
                {/product.additional_images}',
            'expect'   => [
                'product.additional_images|repeat:additional_image' => [
                    TagInterface::CODE          => 'product.additional_images|repeat:additional_image',
                    TagInterface::ENTITY_TYPE   => 'product',
                    TagInterface::PROPERTY_NAME => 'additional_images',
                    TagInterface::MODIFIERS     => ['repeat' => 'additional_image'],
                    TagInterface::IS_PAIRED     => true,
                ],
                'additional_image.url' => [
                    TagInterface::CODE          => 'additional_image.url',
                    TagInterface::ENTITY_TYPE   => 'additional_image',
                    TagInterface::PROPERTY_NAME => 'url',
                    TagInterface::MODIFIERS     => [],
                    TagInterface::IS_PAIRED     => false,
                ],
            ],
        ];
    }
}
