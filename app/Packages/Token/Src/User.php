<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 10:57
 */

namespace App\Packages\Token;


class User
{
    static private $userID = 0;

    static public function setUser($userID)
    {
        self::$userID = $userID;

        return true;
    }

    static public function User()
    {
        return self::$userID;
    }
}