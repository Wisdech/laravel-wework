<?php

namespace XuDev\Wework;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use XuDev\Wework\Core\OAuth;
use XuDev\Wework\Exception\WeworkException;

class WeworkSDK
{
    const CacheKey = 'wework_corp_access_token';

    protected string $corpId;

    protected string $agentId;

    protected string $secret;

    /**
     * 初始化SDK
     *
     * @throws ConnectionException
     * @throws WeworkException
     */
    public function __construct()
    {
        $this->corpId = config('wework.corp_id');
        $this->agentId = config('wework.agent_id');
        $this->secret = config('wework.secret');
    }

    /**
     * 获取 corpId
     */
    public function getCorpId(): string
    {
        return $this->corpId;
    }

    /**
     * 获取 agentId
     */
    public function getAgentId(): string
    {
        return $this->agentId;
    }

    /**
     * OAuth 模块
     */
    public function OAuth(): OAuth
    {
        return new OAuth($this);
    }

    /**
     * 检查Api接口返回值
     *
     * @throws WeworkException
     */
    public function checkResponse(array $result): void
    {
        if (array_key_exists('errcode', $result)) {
            if ($result['errcode'] != 0) {
                throw new WeworkException($result['errcode']);
            }
        } else {
            throw new WeworkException(0, '返回结果检查失败');
        }
    }

    /**
     * 获取AccessToken
     *
     * @throws WeworkException|ConnectionException
     */
    public function getAccessToken(): string
    {
        $accessToken = Cache::get(self::CacheKey);
        if (empty($accessToken)) {
            return $this->refreshAccessToken();
        }

        return $accessToken;
    }

    /**
     * 刷新AccessToken
     *
     * @throws WeworkException|ConnectionException
     */
    private function refreshAccessToken(): string
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';

        $result = Http::get($url, [
            'corpid' => $this->corpId,
            'corpsecret' => $this->secret,
        ])->json();

        $this->checkResponse($result);

        $expiresIn = $result['expires_in'];
        $accessToken = $result['access_token'];

        Cache::put(self::CacheKey, $accessToken, $expiresIn);

        return $accessToken;
    }
}
