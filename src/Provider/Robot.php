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
use Fan\Feishu\AccessToken\TenantAccessToken;
use Fan\Feishu\AccessTokenNeeded;
use GuzzleHttp\RequestOptions;
use Psr\Container\ContainerInterface;

class Robot extends AbstractProvider
{
    use AccessTokenNeeded;

    protected string $name = 'Robot';

    protected Message $message;

    protected string $openId = '';

    public function __construct(ContainerInterface $container, protected array $conf)
    {
        parent::__construct($container);
        $this->setAccessToken(make(TenantAccessToken::class, [
            $this->container,
            $conf['app_id'],
            $conf['app_secret'],
        ]));
        $this->message = $container->get(Message::class);
    }

    /**
     * 机器人信息.
     */
    public function info()
    {
        $ret = $this->client()->get('open-apis/bot/v3/info/', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ],
        ]);

        return $this->handleResponse($ret)['bot'];
    }

    /**
     * 群组列表.
     */
    public function groupList(int $pageSize = 10, string $pageToken = null)
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken) {
            $query['page_token'] = $pageToken;
        }

        $ret = $this->client()->get('open-apis/chat/v4/list', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ],
            RequestOptions::QUERY => $query,
        ]);

        return $this->handleResponse($ret)['data'] ?? [];
    }

    public function getOpenId(): string
    {
        if (empty($this->openId)) {
            $info = $this->info();
            $this->openId = $info['open_id'];
        }

        return $this->openId;
    }

    public function sendText(string $chatId, string $text)
    {
        return $this->message->sendText(
            [
                'open_id' => $this->getOpenId(),
                'chat_id' => $chatId,
            ],
            $text,
            $this->getAccessToken()
        );
    }
}
