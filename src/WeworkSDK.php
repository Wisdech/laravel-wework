<?php

namespace XuDev\Wework;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use XuDev\Wework\Exception\WeworkException;

class WeworkSDK
{
    const CacheKey = 'wework_corp_access_token';

    protected string $host = 'https://qyapi.weixin.qq.com/cgi-bin';

    protected string $corpId;

    protected string $agentId;

    protected string $secret;

    protected ?string $accessToken;

    /**
     * 获取完整请求 uri
     */
    protected function buildUri(string $uri): string
    {
        return $this->host.$uri;
    }

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

        if (! empty($this->corpId) && ! empty($this->secret)) {
            $this->setAccessToken();
        }
    }

    /**
     * 检查Api接口返回值
     *
     * @throws WeworkException
     */
    private function unpackResult(array $result): array
    {
        if (array_key_exists('errcode', $result)) {
            if ($result['errcode'] != 0) {
                throw new WeworkException($result['errcode']);
            }
        } else {
            throw new WeworkException(0, '返回结果检查失败');
        }

        return $result;
    }

    /**
     * 设置AccessToken
     *
     * @throws WeworkException|ConnectionException
     */
    private function setAccessToken(): void
    {
        $this->accessToken = Cache::get(self::CacheKey);
        if (empty($this->accessToken)) {
            $this->accessToken = $this->refreshAccessToken();
        }
    }

    /**
     * 刷新AccessToken
     *
     * @throws WeworkException|ConnectionException
     */
    private function refreshAccessToken(): string
    {
        $url = $this->buildUri('/gettoken');

        $result = Http::get($url, [
            'corpid' => $this->corpId,
            'corpsecret' => $this->secret,
        ])->json();

        $result = $this->unpackResult($result);

        $expiresIn = $result['expires_in'];
        $accessToken = $result['access_token'];

        Cache::put(self::CacheKey, $accessToken, $expiresIn);

        return $accessToken;
    }

    /**
     * 获取授权登录链接
     *
     * @param  string  $redirectUrl  系统回调地址
     * @param  string  $state  随机State
     * @param  bool  $inApp  应用内/网页端，返回不同链接
     * @return string 完整登录地址
     */
    public function buildLoginUri(string $redirectUrl, string $state, bool $inApp = false): string
    {
        return $inApp
            ? 'https://open.weixin.qq.com/connect/oauth2/authorize?'.http_build_query([
                'appid' => $this->corpId,
                'redirect_uri' => $redirectUrl,
                'agentid' => $this->agentId,
                'state' => $state,
                'response_type' => 'code',
                'scope' => 'snsapi_base',
            ]).'#wechat_redirect'
            : 'https://login.work.weixin.qq.com/wwlogin/sso/login?'.http_build_query([
                'login_type' => 'CorpApp',
                'appid' => $this->corpId,
                'agentid' => $this->agentId,
                'redirect_uri' => $redirectUrl,
                'state' => $state,
            ]);
    }

    /**
     * 根据用户授权码获取用户信息
     *
     * @throws WeworkException|ConnectionException
     */
    public function getUserInfo(string $code): array
    {
        $url = $this->buildUri('/auth/userinfo');

        $result = Http::get($url, [
            'code' => $code,
            'access_token' => $this->accessToken,
        ])->json();

        return $this->unpackResult($result);
    }
}
