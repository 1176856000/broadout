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

namespace Plumrocket\Datagenerator\Model\CategoryMapping;

class Search
{
    /**
     * @param string $target
     * @param string $source
     * @return array
     */
    public function search(string $target, string $source)
    {
        try {
            $categories = $this->readSource($source);
        } catch (\TypeError $e) {
            return [];
        }

        $result = [];

        while (! $categories->eof()) {
            if (false !== stripos($categories->current(), $target)) {
                $result[] = $categories->current();
            }

            $categories->next();
        }

        return $result;
    }

    /**
     * @return \Iterator
     */
    private function readSource(string $source)
    {
        return new \SplFileObject($source);
    }
}
