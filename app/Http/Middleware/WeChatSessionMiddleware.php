<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 15:20
 */

namespace App\Http\Middleware;


use App\Packages\Response\Error\Code;
use App\Packages\WeChat\OfficialAccount;

class WeChatSessionMiddleware
{
    private $weChat;

    public function __construct(OfficialAccount $weChat)
    {
        $this->weChat = $weChat;
    }

    public function handle($request, \Closure $next)
    {
        if (!$request->has('openID'))
            return jsonResponse(Code::INVALID_PARAMETER);

        $token = $this->weChat->oauth2AccessTokenByOpenID($request->input('openID'));

        if (!$token)
            return jsonResponse(Code::INVALID_SESSION);

        return $next($request);
    }
}