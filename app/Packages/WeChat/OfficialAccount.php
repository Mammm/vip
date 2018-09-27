<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 17:44
 */

namespace App\Packages\WeChat;

use App\Packages\WeChat\API\API;
use App\Packages\WeChat\API\URL;
use Illuminate\Support\Facades\Cache;

class OfficialAccount extends WeChat
{
    const REFRESH_TOKEN_EXPIRE_MIN = 2592000;

    public function __construct()
    {

    }

    /**
     * 获取授权页面地址
     * @param $redirectUri
     * @param $scope
     * @return string
     */
    public function oauth2AuthorizeUrl($redirectUri, $scope)
    {
        $redirectUri = urlencode($redirectUri);

        $params = [
            'appid' => $this->appID,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope
        ];

        $httpQuery = http_build_query($params);
        $httpQuery = '?'.$httpQuery.'#wechat_redirect';

        return URL::OAUTH2_AUTHORIZE.$httpQuery;
    }

    /**
     * 根据授权后得到的code获取access_token
     * @param $code
     * @return array|bool
     */
    public function oauth2AccessTokenByCode($code)
    {
        $response = API::oauth2AccessToken($this->appID, $this->appSecret, $code);

        if (!$response)
            return false;

        $this->cacheOauth2AccessToken($response);

        return ['token' => $response['access_token'], 'openID' => $response['openid']];
    }

    /**
     * 根据refresh_token重新刷新access_token
     * @param $refreshToken
     * @return array|bool
     */
    public function oauth2AccessTokenByRefreshToken($refreshToken)
    {
        $response = API::oauth2RefreshToken($this->appID, $refreshToken);

        if (!$response)
            return false;

        $this->cacheOauth2AccessToken($response);

        return ['token' => $response['access_token'], 'openID' => $response['openid']];
    }

    /**
     * 根据openID获取缓存中的access_token或者刷新access_token
     * @param $openID
     * @return array|bool
     */
    public function oauth2AccessTokenByOpenID($openID)
    {
        $token = Cache::get($this->oauth2AccessTokenCacheKey($openID));

        if ($token)
            return ['token' => $token, 'openID' => $openID];

        $refreshToken = Cache::get($this->oauth2RefreshTokenCacheKey($openID));

        if ($refreshToken)
            return $this->oauth2AccessTokenByRefreshToken($refreshToken);

        return false;
    }

    /**
     * 缓存access_token信息
     * @param $response
     * @return bool
     */
    private function cacheOauth2AccessToken($response)
    {
        $token = $response['access_token'];
        $refreshToken = $response['refresh_token'];
        $openID = $response['openid'];
        $expireMin = floor((time() + $response['expires_in'] - 300) / 60);

        Cache::put($this->oauth2AccessTokenCacheKey($openID), $token, $expireMin);
        Cache::put($this->oauth2RefreshTokenCacheKey($openID), $refreshToken, self::REFRESH_TOKEN_EXPIRE_MIN);

        return true;
    }

    private function oauth2AccessTokenCacheKey($openID)
    {
        return md5($openID.'-Oauth2AccessToken');
    }

    private function oauth2RefreshTokenCacheKey($openID)
    {
        return md5($openID.'-Oauth2RefreshToken');
    }

    public function oauth2UserInfo($openID, $token)
    {
        $response = API::oauth2UserInfo($openID, $token);

        if (!$response)
            return false;

        return $response;
    }

    public function userInfo($openID)
    {
        $token = $this->accessToken();

        $response = API::userInfo($openID, $token);

        if (!$response)
            return false;

        return $response;
    }


}