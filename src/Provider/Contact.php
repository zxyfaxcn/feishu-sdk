<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Fan\Feishu\Provider;

use Fan\Feishu\AbstractProvider;
use Fan\Feishu\TenantAccessTokenNeeded;
use GuzzleHttp\RequestOptions;
use Psr\Container\ContainerInterface;

class Contact extends AbstractProvider
{
    use TenantAccessTokenNeeded;

    protected string $name = 'Contact';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * 获取单位信息.
     */
    public function unit(string $id)
    {
        $ret = $this->client()->post('open-apis/contact/v3/unit/' . $id, [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $this->getAccessToken()],
        ]);

        return $this->handleResponse($ret)['data'] ?? [];
    }

    /**
     * 获取用户信息.
     */
    public function user(string $id)
    {
        $ret = $this->client()->post('open-apis/contact/v3/users/' . $id, [
            RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $this->getAccessToken()],
        ]);

        return $this->handleResponse($ret)['data'] ?? [];
    }
}