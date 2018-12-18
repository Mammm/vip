<?php
class Message
{
    const DEFAULT_MESSAGE = '未知错误';

    static public function getErrorMessage($code)
    {
        $messageList = [
            Code::SUCCESS => '成功',
            Code::FAILED => '失败',
            Code::INVALID_PARAMETER => '参数异常',
            Code::REMOTE => '远程服务器异常',
            Code::INVALID_SESSION => '登录超时，请重新登录',
            Code::USER_NOT_FOUND => '用户未找到'
        ];

        if (!isset($messageList[$code]))
            return self::DEFAULT_MESSAGE;

        return $messageList[$code];
    }
}