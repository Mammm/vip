<?php
require_once 'URL.php';
require_once 'API.php';

class WeChat
{
    protected $appID;

    protected $appSecret;
    private $cache;
    const REFRESH_TOKEN_EXPIRE_MIN = 2592000;


    public function __construct()
    {
        $this->cache = app('redis');
    }

    public function setAppID($appID)
    {
        $this->appID = $appID;

        return $this;
    }

    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    public function accessToken()
    {
        $token = $this->cache->get($this->accessTokenCacheKey());

        if ($token)
            return $token;

        $response = API::accessToken($this->appID, $this->appSecret);

        if (!$response)
            return false;

        $token = $response['access_token'];

        $this->cache->set($this->accessTokenCacheKey(), $response['access_token'], $response['expires_in']);

        return $token;
    }

    public function clearAccessToken()
    {
        return $this->cache->delete($this->accessTokenCacheKey());
    }

    private function accessTokenCacheKey()
    {
        return md5($this->appID.'access-token-key');
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
        $token = $this->cache->get($this->oauth2AccessTokenCacheKey($openID));

        if ($token)
            return ['token' => $token, 'openID' => $openID];

        $refreshToken = $this->cache->get($this->oauth2RefreshTokenCacheKey($openID));

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

        $this->cache->set($this->oauth2AccessTokenCacheKey($openID), $token, $response['expires_in']);
        $this->cache->set($this->oauth2RefreshTokenCacheKey($openID), $refreshToken, self::REFRESH_TOKEN_EXPIRE_MIN);

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