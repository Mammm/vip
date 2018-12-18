<?php
class WeChatController
{
    private $wechat;

    public function __construct()
    {
        $this->wechat = app('wechat');
    }

    public function accessToken($request)
    {
        if (!paramHas($request, 'code'))
            return jsonResponse(Code::INVALID_PARAMETER);

        $result = $this->wechat->oauth2AccessTokenByCode($request['code']);

        if (!$result)
            return jsonResponse(Code::REMOTE);

        return jsonResponse(Code::SUCCESS, ['openID' => $result['openID']]);
    }
}