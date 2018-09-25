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

        return $this;
    }

    public function setTokenExpire($expire)
    {
        $this->tokenExpire = $expire;

        return $this;
    }

    public function setRefreshTokenExpire($expire)
    {
        $this->refreshTokenExpire = $expire;

        return $this;
    }

    public function make($userID)
    {
        $payload = $this->payload($userID);
        $sign = $this->makeSign($payload);

        $token = compact('payload', 'sign');

        return $this->tokenEncode($token);
    }

    public function checkSign($token)
    {
        $token = $this->tokenDecode($token);

        $sign = $this->makeSign($token['payload']);

        if ($sign != $token['sign'])
            return false;

        return true;
    }

    public function refresh($token)
    {
        $token = $this->tokenDecode($token);
        $payload = $token['payload'];

        if ($payload['re_exp'] < time())
            return false;

        $payload = $this->payload($payload['sub'], $payload['re_exp']);
        $sign = $this->makeSign($payload);

        $token = compact('payload', 'sign');

        return $this->tokenEncode($token);
    }

    public function isExpire($token)
    {
        $token = $this->tokenDecode($token);

        if ($token['payload']['exp'] > time())
            return false;

        return true;
    }

    public function reIsExpire($token)
    {
        $token = $this->tokenDecode($token);

        if ($token['payload']['re_exp'] > time())
            return false;

        return true;
    }

    public function getUserID($token)
    {
        $token = $this->tokenDecode($token);

        return $token['payload']['sub'];
    }

    private function payload($userID, $reExp = 0)
    {
        $payload = [
            'iss' => 'mpr',
            'exp' => time() + $this->tokenExpire,
            're_exp' => 0,
            'sub' => $userID
        ];

        if (!$reExp) {
            $payload['re_exp']  = time() + $this->refreshTokenExpire;

        } else {
            $payload['re_exp'] = $reExp;

        }

        return $payload;
   }

   private function makeSign(array $param)
   {
       ksort($param, SORT_STRING);

       return strtoupper(md5(json_encode($param).$this->secret));
   }

   private function tokenEncode($token)
   {
       return base64_encode(json_encode($token));
   }

   private function tokenDecode($token)
   {
       return json_decode(base64_decode($token), true);
   }
}