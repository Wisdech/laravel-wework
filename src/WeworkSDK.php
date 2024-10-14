<?php

namespace Wisdech\Wework;

use Wisdech\Wework\Exception\WeworkException;
use Wisdech\Wework\Traits\HasCache;
use Wisdech\Wework\Traits\HasClient;
use Wisdech\Wework\Traits\PathBuilder;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Cache\InvalidArgumentException;

class WeworkSDK
{
    use PathBuilder, HasCache, HasClient;

    protected string $host = 'https://qyapi.weixin.qq.com/cgi-bin';
    protected string $cacheKey = 'wework_corp_access_token';
    protected ?string $accessToken;

    /**
     * 初始化SDK
     * @param string $corpId 企业微信 CorpID
     * @param string $agentId 企业微信 AgentID
     * @param string $secret 企业微信 Secret
     * @throws WeworkException
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function __construct(protected string $corpId, protected string $agentId, protected string $secret)
    {
        $this->setCacheItem();
        $this->setHttpClient();
        $this->setAccessToken();
    }

    /**
     * 检查Api接口返回值
     * @param string $result
     * @return array
     * @throws WeworkException
     */
    private function unpackResult(string $result): array
    {
        $result = json_decode($result, true);

        if (key_exists('errcode', $result)) {
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
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws WeworkException
     */
    private function setAccessToken(): void
    {
        if (empty($this->cache)) {
            $this->setCacheItem();
        }

        $this->accessToken = $this->cache->get();
        if (empty($this->accessToken)) {
            $this->accessToken = $this->refreshAccessToken();
        }
    }

    /**
     * 刷新AccessToken
     * @return string
     * @throws GuzzleException
     * @throws WeworkException
     */
    private function refreshAccessToken(): string
    {
        $url = $this->buildUri('/gettoken');

        $result = $this->client->get($url, [
            'query' => [
                'corpid' => $this->corpId,
                'corpsecret' => $this->secret,
            ]
        ])->getBody()->getContents();

        $result = $this->unpackResult($result);

        $expiresIn = $result['expires_in'];
        $accessToken = $result['access_token'];

        $this->cache->set($accessToken)->expiresAfter($expiresIn);

        return $accessToken;
    }


    /**
     * 获取授权登录链接
     * @param string $redirectUrl 系统回调地址
     * @param string $state 随机State
     * @param bool $inApp 应用内/网页端，返回不同链接
     * @return string 完整登录地址
     */
    public function buildLoginUri(string $redirectUrl, string $state, bool $inApp = false): string
    {
        return $inApp
            ? "https://open.weixin.qq.com/connect/oauth2/authorize?" . http_build_query([
                'appid' => $this->corpId,
                'redirect_uri' => $redirectUrl,
                'agentid' => $this->agentId,
                'state' => $state,
                'response_type' => 'code',
                'scope' => 'snsapi_base',
            ]) . '#wechat_redirect'
            : "https://login.work.weixin.qq.com/wwlogin/sso/login?" . http_build_query([
                'login_type' => 'CorpApp',
                'appid' => $this->corpId,
                'agentid' => $this->agentId,
                'redirect_uri' => $redirectUrl,
                'state' => $state,
            ]);
    }


    /**
     * 根据用户授权码获取用户信息
     * @param string $code
     * @return array
     * @throws GuzzleException
     * @throws WeworkException
     */
    public function getUserInfo(string $code): array
    {
        $url = $this->buildUri('/auth/getuserinfo');

        $result = $this->client->get($url, [
            'query' => [
                'code' => $code,
                'access_token' => $this->accessToken,
            ]
        ])->getBody()->getContents();

        return $this->unpackResult($result);
    }
}
