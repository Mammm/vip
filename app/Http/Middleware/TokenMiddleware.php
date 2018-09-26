<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 10:42
 */

namespace App\Http\Middleware;


use App\Packages\Response\Error\Code;
use App\Packages\Token\Token;
use App\Packages\Token\User;

class TokenMiddleware
{
    private $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function handle($request, \Closure $next)
    {
        if (!$request->has('token'))
            return jsonResponse(Code::FAILED);

        $token = $request->input('token');

        if (!$this->token->checkSign($token))
            return jsonResponse(Code::FAILED);

        if ($this->token->isExpire($token))
            return jsonResponse(Code::FAILED);

        User::setUser($this->token->getUserID($token));

        return $next($request);
    }
}