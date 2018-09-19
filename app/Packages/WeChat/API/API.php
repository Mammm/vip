<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 16:23
 */
namespace App\Packages\WeChat\API;

class API
{
    static private function response($apiResponse)
    {
        if (!isset($apiResponse[0]))
            return false;

        $apiResponse = json_decode($apiResponse, true);

        if (isset($apiResponse['errcode'])) {
            //日志;
            return false;
        }

        return $apiResponse;
    }

    static public function accessToken($appId, $appSecret)
    {
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appId,
            'secret' => $appSecret
        ];

        return self::response(httpRequest(URL::ACCESS_TOKEN, $params, 'GET'));
    }

    static public function oauth2AccessToken($appId, $appSecret, $code)
    {
        $params = [
            'code' => $code,
            'appid' => $appId,
            'secret' => $appSecret,
            'grant_type' => 'authorization_code'
        ];

        return self::response(httpRequest(URL::OAUTH2_ACCESS_TOKEN, $params, 'GET'));
    }
}