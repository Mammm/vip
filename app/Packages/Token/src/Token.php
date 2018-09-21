<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/21
 * Time: 9:35
 */
namespace App\Packages\Token;

class Token
{
    private $secret;
    private $tokenExpire;
    private $refreshTokenExpire;

    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    public function setTokenExpire($expire)
    {
        $this->tokenExpire = $expire;
    }

    public function setRefreshTokenExpire($expire)
    {
        $this->refreshTokenExpire = $expire;
    }


    public function make($userID)
    {
        $payload = $this->payload($userID);
    }

    private function payload($userID)
    {
        $payload = [
            'iss' => 'mpr',
            'exp' => time() + $this->tokenExpire,
            'sub' => $userID
        ];

        $payload['re_exp'] = time() + $this->refreshTokenExpire;
        $payload['re_token'] = md5(json_encode($payload).$this->secret) ;

        return base64_encode(json_encode($payload));
   }

    public function refresh()
    {

    }

    public function check()
    {
    }
}