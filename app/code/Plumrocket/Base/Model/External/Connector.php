<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\External;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Plumrocket\Base\Helper\Config;

class Connector
{
    /**
     * @var \Plumrocket\Base\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Plumrocket\Base\Helper\Config                   $config
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(Config $config, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * @param string $url
     * @param array  $data
     * @return array|bool|float|int|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function connect(string $url, array $data = [])
    {
        $data['v'] = 1;
        $query     = http_build_query($data);
        $ch        = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);

        if ($this->config->isDebugMode() && curl_errno($ch)) {
            throw new \Exception('cURL Error: ' . curl_error($ch), 1);
        }

        curl_close($ch);

        if (false === $res) {
            throw new LocalizedException(__('Cannot connect to %1', $url));
        }

        return $this->serializer->unserialize($res);
    }
}
