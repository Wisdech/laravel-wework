<?php

namespace XuDev\Wework\SDK;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use XuDev\Wework\Exception\StateErrorException;
use XuDev\Wework\Exception\WeworkException;
use XuDev\Wework\Model\WeUser;
use XuDev\Wework\WeworkApi;

class OAuth
{
    private string $webHost = 'https://login.work.weixin.qq.com/wwlogin/sso/login';

    private string $appHost = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    private string $redirectUri;

    public function __construct(protected WeworkApi $sdk)
    {
        $this->redirectUri = Uri::to(config('wework.redirect_uri'));
    }

    /**
     * 浏览器Web扫码登录
     */
    public function redirectInWeb(Request $request, string $type = 'CorpApp'): RedirectResponse
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'login_type' => $type,
            'appid' => $this->sdk->getCorpId(),
            'agentid' => $this->sdk->getAgentId(),
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
        ]);

        return Response::redirectTo("$this->webHost?$query");
    }

    /**
     * 企业微信APP内登录
     */
    public function redirectInApp(Request $request): RedirectResponse
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'appid' => $this->sdk->getCorpId(),
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'snsapi_privateinfo',
            'state' => $state,
            'agentid' => $this->sdk->getAgentId(),
        ]);

        return Response::redirectTo("$this->appHost?$query#wechat_redirect");
    }

    /**
     * 用户授权完成回调
     *
     * @throws ConnectionException
     * @throws StateErrorException
     * @throws WeworkException
     */
    public function callback(Request $request): ?array
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/auth/getuserinfo';

        $state = $request->session()->pull('state');

        if ($state !== $request->state) {
            throw new StateErrorException;
        }

        $response = Http::get($url, [
            'access_token' => $this->sdk->getAccessToken(),
            'code' => $request->code,
        ])->json();

        $this->sdk->checkResponse($response);

        if (array_key_exists('userid', $response)) {

            if (array_key_exists('user_ticket', $response)) {
                return [
                    'type' => 'userid',
                    'id' => $response['userid'],
                    'ticket' => $response['user_ticket'],
                ];
            }

            return [
                'type' => 'userid',
                'id' => $response['userid'],
            ];
        }

        if (array_key_exists('openid', $response)) {

            if (array_key_exists('external_userid', $response)) {
                return [
                    'type' => 'openid',
                    'id' => $response['openid'],
                    'external_user_id' => $response['external_user_id'],
                ];
            }

            return [
                'type' => 'openid',
                'id' => $response['openid'],
            ];
        }

        return null;
    }

    public function getUserDetail(string $ticket): WeUser
    {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/auth/getuserdetail?access_token=';
        $url .= $this->sdk->getAccessToken();

        $response = Http::asJson()->post($url,
            ['user_ticket' => $ticket]
        )->json();

        $this->sdk->checkResponse($response);

        return new WeUser($response);
    }
}
