<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/25
 * Time: 17:45
 */

namespace App\Packages\Response\Error;


class Message
{
    const DEFAULT_MESSAGE = '未知错误';

    static public function getErrorMessage($code)
    {
        $messageList = [
            Code::SUCCESS => '成功',
            Code::FAILED => '失败',
        ];

        if (!isset($messageList[$code]))
            return self::DEFAULT_MESSAGE;

        return $messageList[$code];
    }
}