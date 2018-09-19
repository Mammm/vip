<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 17:44
 */

namespace App\Packages\WeChat;


use App\Packages\WeChat\API\API;

class OfficialAccounts extends WeChat
{
    public function __construct()
    {

    }

    public function oauth2AccessToken($code)
    {
        $response = API::oauth2AccessToken($this->appId, $this->appSecret, $code);

        if (!$response)
            return false;

        return $response;
    }


}