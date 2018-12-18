<?php
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

    static public function accessToken($appID, $appSecret)
    {
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appID,
            'secret' => $appSecret
        ];

        return self::response(httpRequest(URL::ACCESS_TOKEN, $params, 'GET'));
    }

    static public function oauth2AccessToken($appID, $appSecret, $code)
    {
        $params = [
            'code' => $code,
            'appid' => $appID,
            'secret' => $appSecret,
            'grant_type' => 'authorization_code'
        ];

        return self::response(httpRequest(URL::OAUTH2_ACCESS_TOKEN, $params, 'GET'));
    }

    static public function oauth2RefreshToken($appID, $refreshToken)
    {
        $params = [
            'appid' => $appID,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];

        return self::response(httpRequest(URL::OAUTH2_REFRESH_TOKEN, $params, 'GET'));
    }

    static public function oauth2UserInfo($openID, $token)
    {
        $params = [
            'openid' => $openID,
            'access_token' => $token,
            'lang' => 'zh_CN'
        ];

        return self::response(httpRequest(URL::OAUTH2_USER_INFO, $params, 'GET'));
    }

    static public function userInfo($openID, $token)
    {
        $params = [
            'openid' => $openID,
            'access_token' => $token,
            'lang' => 'zh_CN'
        ];

        return self::response(httpRequest(URL::OA_USER_INFO, $params, 'GET'));
    }
}