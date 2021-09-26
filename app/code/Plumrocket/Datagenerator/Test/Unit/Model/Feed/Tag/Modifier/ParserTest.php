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

namespace Plumrocket\Datagenerator\Test\Unit\Model\Feed\Tag\Modifier;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Plumrocket\Datagenerator\Model\Feed\Tag\Modifier\Parser;

/**
 * @since 1.0.0
 */
class ParserTest extends TestCase
{
    /**
     * @var \Plumrocket\Datagenerator\Model\Feed\Tag\Modifier\Parser
     */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = (new ObjectManager($this))->getObject(Parser::class);
    }

    /**
     * @dataProvider parts
     * @param array $parts
     * @param array $expect
     */
    public function testPostRequestExtension(array $parts, array $expect): void
    {
        self::assertSame($expect, $this->parser->parse($parts));
    }

    public function parts(): \Generator
    {
        yield [
            'parts' => [],
            'expect' => [],
        ];
        yield [
            'parts' => [''],
            'expect' => [],
        ];
        yield [
            'parts' => [' '],
            'expect' => [],
        ];
        yield [
            'parts' => ['size'],
            'expect' => [],
        ];
        yield [
            'parts' => ['size: '],
            'expect' => ['size' => ''],
        ];
        yield [
            'parts' => ['size:150'],
            'expect' => ['size' => '150'],
        ];
        yield [
            'parts' => ['size:150:150'],
            'expect' => ['size' => '150:150'],
        ];
    }
}
