<?php

namespace App\Packages\WeChat;


use App\Packages\WeChat\API\API;
use Illuminate\Support\Facades\Cache;

class WeChat
{
    protected $appId;

    protected $appSecret;

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function accessToken()
    {
        $token = Cache::get($this->accessTokenCacheKey());

        if ($token)
            return $token;

        $response = API::accessToken($this->appId, $this->appSecret);

        if (!$response)
            return false;

        $token = $response['access_token'];
        $expireMin = floor((time() + $response['expires_in'] - 300) / 60);

        Cache::put($this->accessTokenCacheKey(), $response['access_token'], $expireMin);

        return $token;
    }

    public function clearAccessToken()
    {
        return Cache::forget($this->accessTokenCacheKey());
    }

    private function accessTokenCacheKey()
    {
        return md5($this->appId.'access-token-key');
    }
}